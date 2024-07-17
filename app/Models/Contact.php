<?php

// app/Models/Contact.php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone'];

    public function scopeSearch(Builder $query, array $filters): Builder
    {
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (isset($filters['phone'])) {
            $query->where('phone', 'like', '%' . $filters['phone'] . '%');
        }

        return $query;
    }

    public static function createContact(array $data)
    {
        return self::create($data);
    }

    public static function deleteByPhone(string $phone): array
    {
        $contact = self::where('phone', $phone)->first();

        if ($contact) {
            $contact->delete();
            return ['message' => 'Contact deleted successfully'];
        } else {
            throw new ModelNotFoundException('Contact not found');
        }
    }

    public static function updateByPhone(string $phone, array $data)
    {
        $contact = self::where('phone', $phone)->first();

        if ($contact) {
            $contact->update($data);
            return $contact;
        } else {
            throw new ModelNotFoundException('Contact not found');
        }
    }
}
