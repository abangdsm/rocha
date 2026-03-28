<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ContactsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Auth::user()->contacts;
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'Phone', 'Email', 'Address', 'Notes', 'Created At'
        ];
    }

    public function map($contact): array
    {
        return [
            $contact->id,
            $contact->name,
            $contact->phone,
            $contact->email,
            $contact->address,
            $contact->notes,
            $contact->created_at->format('Y-m-d H:i:s'),
        ];
    }
}