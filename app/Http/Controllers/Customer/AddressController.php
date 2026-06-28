<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\CustomerAddress;

class AddressController extends Controller
{
    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();

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

    public function update(UpdateAddressRequest $request, CustomerAddress $address)
    {
        if ($address->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $data = $request->validated();

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

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $data = $request->validated();

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
