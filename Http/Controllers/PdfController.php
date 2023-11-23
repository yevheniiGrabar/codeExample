<?php

namespace App\Http\Controllers;

use App\Services\PdfService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group PDF
 *
 * Endpoints for managing PDFs
 */
class PdfController extends Controller
{
    public PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * List
     *
     * Returns list of available PDFs
     * @authenticated
     */
    public function index(Request $request): Factory|View|Application
    {
        $data['name'] = $request->get('name');
        $data['from'] = $request->get('from');
        $data['to'] = $request->get('to');
        $data['cc'] = $request->get('cc');
        $data['subject'] = $request->get('subject');
        $data['emailBody'] = $request->get('body');

        $attachments[] = $request->file('attachments');

        $mail = Mail::send([],$data, function ($message) use($data, $attachments) {
            $message->to($data['to'])
                ->subject($data['subject']);

            if(sizeof($attachments) > 0) {
                foreach($attachments as $attachment) {
                    $message->attach($attachment->getRealPath(), array(
                        'as' => $attachment->getClientOriginalName(), // If you want you can chnage original name to custom name
                        'mime' => $attachment->getMimeType())
                    );
                }
            }
        });

        return view('email');
    }

    /**
     * Preview PDF
     *
     * Generate Pdf file by params from request to preview & download(pdf)
     * @authenticated
     * @param Request $request
     * @return Response|BinaryFileResponse
     */
    public function downloadPdf(Request $request): Response|BinaryFileResponse
    {
        return $this->pdfService->generatePreviewPdf($request);
    }
}
