<?php

// app/Http/Controllers/ContactController.php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    // Retrieve all contacts or filter by query parameters
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'email', 'phone']);
        $contacts = Contact::search($filters)->get();

        return response()->json($contacts);
    }

    // Retrieve a contact by specific filters
    public function show(Request $request)
    {
        $filters = $request->only(['name', 'email', 'phone']);
        $result = Contact::search($filters)->get();

        return response()->json($result);
    }

    // Create a new contact
    public function store(Request $request)
    {
        try {
            // Use the model method to create a new contact
            $contact = Contact::createContact($request->all());

            // Return the created contact
            return response()->json($contact, 201);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json($e->errors(), 422);
        }
    }

    // Delete a contact by phone number
    public function destroy(Request $request)
    {
        // Validate the phone number
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        // Use the model method to delete the contact by phone number
        $response = Contact::deleteByPhone($request->phone);

        // Return the response
        if ($response['message'] === 'Contact deleted successfully') {
            return response()->json($response, 200);
        } else {
            return response()->json($response, 404);
        }
    }
}
