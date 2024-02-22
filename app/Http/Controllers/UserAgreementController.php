<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentVersion;
use App\Models\UserAgreement;

class UserAgreementController extends Controller
{
    public function checkAgreements(Request $request)
    {   
        $request->validate([
            'device_uuid' => 'required|string',
        ]);

        $device_uuid = $request->input('device_uuid');

        // Fetch relevant active documents for the user
        $relevantDocuments = $this->getRelevantActiveDocuments();

        // Find all agreements for this device_uuid
        $agreedDocumentVersions = UserAgreement::where('device_uuid', $device_uuid)
                                                ->pluck('document_version_id')->toArray();

        // Filter relevant documents that haven't been agreed to
        $documentsToAgree = $relevantDocuments->reject(function ($document) use ($agreedDocumentVersions) {
            return in_array($document->id, $agreedDocumentVersions);
        })->values();

        if ($documentsToAgree->isEmpty()) {
            return response()->json(['message' => 'All relevant active documents have been agreed to.'], 200);
        } else {
            return response()->json($documentsToAgree, 200);
        }
    }

    protected function getRelevantActiveDocuments()
    {
        return DocumentVersion::where('is_active', true)
                              // Add conditional logic based on $userCountry or other factors
                              ->get(['id', 'document_name', 'document_url']);
    }

    public function signDocument(Request $request)
    {
        $request->validate([
            'device_uuid' => 'required|string',
            'document_version_id' => 'required|exists:document_versions,id',
        ]);

        $agreement = UserAgreement::create([
            'device_uuid' => $request->device_uuid,
            'document_version_id' => $request->document_version_id,
        ]);

        return response()->json(['message' => 'Document signed successfully.'], 201);
    }
}