<?php


namespace App\Services;


use App\Models\OrderLine;
use App\Models\Product;
use App\Models\SaleOrderLine;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PdfService
{
    /**
     * Generate and download a preview PDF based on the provided data and type.
     *
     * @param Request $request The HTTP request containing line_ids and type.
     * @return Response|BinaryFileResponse
     */
    public function generatePreviewPdf(Request $request): Response|BinaryFileResponse
    {
        $orders = $request->get('orders');
        $type = $request->get('type');

        if (isset($orders) && sizeof($orders) > 0) {
            $productIds = []; // Initialize an array to store unique product IDs
            $lines = [];
            $prices = [];
            $subTotals = [];
            $totalTax = 0;

            foreach ($orders as $order) {
                if (isset($order['product'])) {
                    $productId = $order['product'];

                    if (!in_array($productId, $productIds)) {
                        $productIds[] = $productId; // Add unique product ID to the array
                        $product = Product::find($productId);

                        if ($product) {
                            $quantity = $order['quantity'];
                            $unitPrice = $order['unit_price'];
                            $discount = $order['discount'];
                            $tax = $order['tax'];

                            $product['quantity'] = $quantity;
                            $product['unit_price'] = $unitPrice;
                            $product['discount'] = $discount;
                            $product['tax'] = $tax;
                            $lines[] = $product;
                        }
                    }
                }
            }

            if ($type === 'purchase') {
                $viewName = 'PDF.purchaseOrderPreview';
                $fileName = 'PurchaseOrder.pdf';
            } elseif ($type === 'sale') {
                $viewName = 'PDF.saleOrderPreview';
                $fileName = 'SaleOrder.pdf';
            }

            $data = [
                'items' => $lines,
            ];
        }

        $pdf = PDF::loadView($viewName, $data);

        return $pdf->download($fileName);
    }
}
