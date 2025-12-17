<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function sendContact(SendContactRequest $request)
    {
        $data=$request->all();
        Contact::create($data);
        return response()->res(success(), 'contact_send', [], 200);
    }
}
