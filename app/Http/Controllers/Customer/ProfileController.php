<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Order;
use App\Models\CustomerAddress;
use App\Models\Cart;
use App\Models\Wilayah;
use App\Models\Wishlist;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)
            ->with(['designRequest', 'payment', 'orderItems', 'itemDetails', 'review'])
            ->latest()
            ->get()
            ->each(function ($order) {
                if ($order->designRequest) {
                    $allFiles = [];
                    if ($order->designRequest->logo) {
                        $allFiles[] = [
                            'name' => 'Logo Tim',
                            'path' => $order->designRequest->logo,
                            'is_logo' => true,
                        ];
                    }
                    if ($order->designRequest->design_files) {
                        foreach ($order->designRequest->design_files as $f) {
                            $allFiles[] = $f;
                        }
                    }
                    $order->designRequest->all_design_files = $allFiles;
                }
            });

        $addresses = CustomerAddress::where('user_id', $user->id)->latest()->get();
        $cartItems = Cart::with('product.category')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $provinces = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->orderBy('nama')->get()->map(fn($i) => ['id' => $i->kode, 'name' => $i->nama]);

        $wishlistItems = Wishlist::with('product.category')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn($w) => [
                'id' => $w->product->id,
                'name' => $w->product->name,
                'price' => $w->product->price,
                'image' => $w->product->image ? asset('storage/' . $w->product->image) : null,
                'category' => $w->product->category?->name,
                'slug' => $w->product->slug ?? $w->product->id,
                'wishlist_id' => $w->id,
            ]);

        return view('customer.profile', [
            'user' => $user,
            'orders' => $orders,
            'addresses' => $addresses,
            'cartItems' => $cartItems,
            'provinces' => $provinces,
            'wishlistItems' => $wishlistItems,
        ]);
    }

    public function update(ProfileUpdateRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        $user = $request->user();
        $user->fill($request->safe()->except(['avatar']));

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            $filename = 'avatar_' . $user->id . '_' . time() . '.jpg';
            $destinationPath = storage_path('app/public/avatars/' . $filename);

            if (!file_exists(storage_path('app/public/avatars'))) {
                mkdir(storage_path('app/public/avatars'), 0755, true);
            }

            $image = strtolower($file->getClientOriginalExtension()) === 'png'
                ? @imagecreatefrompng($file->getRealPath())
                : @imagecreatefromjpeg($file->getRealPath());

            if ($image) {
                imagejpeg($image, $destinationPath, 60);
                imagedestroy($image);
            }

            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = 'avatars/' . $filename;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui!',
                'user'    => $user->fresh(),
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('beranda');
    }
}
