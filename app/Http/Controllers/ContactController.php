<?php

// app/Http/Controllers/ContactController.php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'email', 'phone']);
        $contacts = Contact::search($filters)->get();

        return ContactResource::collection($contacts);
    }

    public function show($phone)
    {
        try {
            $contact = Contact::where('phone', $phone)->firstOrFail();
            return new ContactResource($contact);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Contact not found'], 404);
        }
    }

    public function store(ContactRequest $request)
    {
        try {
            // Validation is handled by ContactRequest
            $contact = Contact::createContact($request->validated());
            return new ContactResource($contact);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database query error while creating contact', ['exception' => $e]);
            return response()->json(['message' => 'Database query error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error while creating contact', ['exception' => $e]);
            return response()->json(['message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function update(ContactRequest $request, $phone)
    {
        try {
            $contact = Contact::where('phone', $phone)->firstOrFail();
            $contact->update($request->validated());
            return new ContactResource($contact);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Contact not found'], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database query error while updating contact', ['exception' => $e]);
            return response()->json(['message' => 'Database query error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error while updating contact', ['exception' => $e]);
            return response()->json(['message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($phone)
    {
        try {
            $response = Contact::deleteByPhone($phone);
            return response()->json($response, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Contact not found'], 404);
        } catch (\Exception $e) {
            Log::error('Unexpected error while deleting contact', ['exception' => $e]);
            return response()->json(['message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
}
