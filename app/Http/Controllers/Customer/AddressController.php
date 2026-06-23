<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'nullable|string|max:255',
            'province'       => 'required|string|max:255',
            'city'           => 'required|string|max:255',
            'district'       => 'required|string|max:255',
            'detail_address' => 'required|string|max:2000',
            'postal_code'    => 'required|string|max:10',
            'address_type'   => 'required|in:rumah,kantor',
        ]);

        $address = CustomerAddress::create([
            'user_id'        => auth()->id(),
            'first_name'     => $data['first_name'],
            'last_name'      => $data['last_name'],
            'province'       => $data['province'],
            'city'           => $data['city'],
            'district'       => $data['district'],
            'detail_address' => $data['detail_address'],
            'postal_code'    => $data['postal_code'],
            'address_type'   => $data['address_type'],
            'is_primary'     => true,
        ]);

        return response()->json([
            'success' => true,
            'address' => $address,
            'message' => 'Alamat baru berhasil disimpan.'
        ]);
    }

    public function update(Request $request, CustomerAddress $address)
    {
        if ($address->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'nullable|string|max:255',
            'province'       => 'required|string|max:255',
            'city'           => 'required|string|max:255',
            'district'       => 'required|string|max:255',
            'detail_address' => 'required|string|max:2000',
            'postal_code'    => 'required|string|max:10',
            'address_type'   => 'required|in:rumah,kantor',
            'is_primary'     => 'nullable|boolean',
        ]);

        if (!empty($data['is_primary'])) {
            auth()->user()->addresses()->update(['is_primary' => false]);
        }

        $address->update($data);

        return response()->json([
            'success' => true,
            'address' => $address->fresh(),
            'message' => 'Alamat berhasil diperbarui.'
        ]);
    }

    public function destroy(CustomerAddress $address)
    {
        if ($address->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil dihapus.'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'fullname' => 'nullable|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'required|string|max:20',
        ]);

        $user->fill($data);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        return response()->json([
            'success' => true,
            'user'    => [
                'name'     => $user->name,
                'fullname' => $user->fullname,
                'email'    => $user->email,
                'phone'    => $user->phone,
            ],
            'message' => 'Profil berhasil diperbarui.'
        ]);
    }
}
