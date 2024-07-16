<?php

// app/Http/Controllers/ContactController.php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

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
            $contact = Contact::createContact($request->all());
            return response()->json($contact, 201);
        } catch (ValidationException $e) {
            Log::error('Validation error while creating contact', ['errors' => $e->errors()]);
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error while creating contact', ['exception' => $e]);
            return response()->json(['message' => 'An unexpected error occurred'], 500);
        }
    }

    // Delete a contact by phone number
    public function destroy(Request $request)
    {
        $request->validate(['phone' => 'required|string|max:20']);

        try {
            $response = Contact::deleteByPhone($request->phone);
            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            Log::warning('Contact not found for deletion', ['phone' => $request->phone]);
            return response()->json(['message' => 'Contact not found'], 404);
        } catch (\Exception $e) {
            Log::error('Unexpected error while deleting contact', ['exception' => $e]);
            return response()->json(['message' => 'An unexpected error occurred'], 500);
        }
    }

    // Update a contact by phone number
    public function update(Request $request)
    {
        $request->validate(['phone' => 'required|string|max:20']);

        try {
            $contact = Contact::updateByPhone($request->phone, $request->all());

            return response()->json($contact, 200);
        } catch (ValidationException $e) {
            Log::error('Validation error while updating contact', ['errors' => $e->errors()]);
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            Log::warning('Contact not found for update', ['phone' => $request->phone]);
            return response()->json(['message' => 'Contact not found'], 404);
        } catch (\Exception $e) {
            Log::error('Unexpected error while updating contact', ['exception' => $e]);
            return response()->json(['message' => 'An unexpected error occurred'], 500);
        }
    }
}
