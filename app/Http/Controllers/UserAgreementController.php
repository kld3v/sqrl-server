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
            // If there are no documents to sign, return null for documents_to_sign
            return response()->json(['documents_to_sign' => null], 200);
        } else {
            // Otherwise, return the documents to sign in the specified structure
            return response()->json(['documents_to_sign' => $documentsToAgree], 200);
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
            // Validate that document_version_id is an array and each element exists in the document_versions table
            'document_version_ids' => 'required|array',
            'document_version_ids.*' => 'exists:document_versions,id',
        ]);
    
        // Iterate over each document_version_id and create a new UserAgreement entry
        foreach ($request->document_version_ids as $documentVersionId) {
            UserAgreement::create([
                'device_uuid' => $request->device_uuid,
                'document_version_id' => $documentVersionId,
            ]);
        }
    
        return response()->json(['message' => 'Documents signed successfully.'], 201);
    }    
}