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

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)
            ->with(['designRequest', 'payment', 'orderItems'])
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

        return view('customer.profile', [
            'user' => $user,
            'orders' => $orders,
            'addresses' => $addresses,
            'cartItems' => $cartItems,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->except(['avatar']));

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

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
