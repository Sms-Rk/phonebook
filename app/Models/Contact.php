<?php

// app/Models/Contact.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone'];

    // Define the query scope for dynamic searching
    public function scopeSearch($query, $filters)
    {
        if (isset($filters['name'])) {
            $query->where('name', 'LIKE', '%' . $filters['name'] . '%');
        }

        if (isset($filters['email'])) {
            $query->where('email', 'LIKE', '%' . $filters['email'] . '%');
        }

        if (isset($filters['phone'])) {
            $query->where('phone', 'LIKE', '%' . $filters['phone'] . '%');
        }

        return $query;
    }

    // Method to handle creation of a new contact
    public static function createContact(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:contacts',
            'phone' => 'required|string|max:20|unique:contacts',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return self::create($validator->validated());
    }

    // Method to handle deletion of a contact by phone number
    public static function deleteByPhone(string $phone)
    {
        $contact = self::where('phone', $phone)->first();

        if ($contact) {
            $contact->delete();
            return ['message' => 'Contact deleted successfully'];
        } else {
            throw new ModelNotFoundException('Contact not found');
        }
    }

    // Method to handle updating a contact by phone number
    public static function updateByPhone(string $phone, array $data)
    {
        $contact = self::where('phone', $phone)->first();

        if ($contact) {
            $validator = Validator::make($data, [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:contacts,email,' . $contact->id,
                'phone' => 'sometimes|required|string|max:20|unique:contacts,phone,' . $contact->id,
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $contact->update($validator->validated());
            return $contact;
        } else {
            throw new ModelNotFoundException('Contact not found');
        }
    }
}
