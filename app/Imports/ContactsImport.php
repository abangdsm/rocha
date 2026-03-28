<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class ContactsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Contact([
            'user_id' => Auth::id(),
            'name' => $row['name'],
            'phone' => $row['phone'],
            'email' => $row['email'] ?? null,
            'address' => $row['address'] ?? null,
            'notes' => $row['notes'] ?? null,
        ]);
    }
}