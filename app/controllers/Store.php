<?php

class Store extends Controller
{
    private $storeModel;
    private $legacyModel;

    public function __construct()
    {
        $this->storeModel = $this->model('StoreEcommerceModel');
        $this->legacyModel = $this->model('StoreManagementModel');
    }

    private function respondJson(array $payload, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function normalizeLegacyItems(array $legacyItems)
    {
        $products = [];
        foreach ($legacyItems as $legacy) {
            $variant = (object) [
                'variant_id' => 0,
                'size' => 'One Size',
                'price' => (float) ($legacy->price ?? 0),
                'stock_qty' => (int) ($legacy->quantity ?? 0),
            ];

            $products[] = (object) [
                'product_id' => (int) ($legacy->item_id ?? 0),
                'product_name' => (string) ($legacy->item_name ?? 'Item'),
                'description' => (string) ($legacy->description ?? ''),
                'category' => (string) ($legacy->category ?? 'Merchandise'),
                'product_image' => (string) ($legacy->item_image ?? ''),
                'is_active' => strcasecmp((string) ($legacy->status ?? 'Available'), 'Sold Out') === 0 ? 0 : 1,
                'min_price' => (float) ($legacy->price ?? 0),
                'total_stock' => (int) ($legacy->quantity ?? 0),
                'variants' => [$variant],
            ];
        }

        return $products;
    }

    public function index()
    {
        try {
            $items = $this->storeModel->getPublicProductsWithVariants();
            if (empty($items) && $this->legacyModel) {
                $items = $this->normalizeLegacyItems($this->legacyModel->getAll());
            }
        } catch (Exception $e) {
            $items = [];
        }

        $this->view('store', [
            'items' => $items,
        ]);
    }

    public function checkout()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->respondJson(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            if (!is_array($payload)) {
                throw new Exception('Invalid checkout payload');
            }

            $buyer = [
                'buyer_name' => trim((string) ($payload['buyer_name'] ?? '')),
                'buyer_phone' => trim((string) ($payload['buyer_phone'] ?? '')),
                'buyer_email' => trim((string) ($payload['buyer_email'] ?? '')),
                'buyer_address' => trim((string) ($payload['buyer_address'] ?? '')),
            ];

            $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
            $result = $this->storeModel->placeGuestOrder($buyer, $items);

            $this->respondJson([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $result,
            ]);
        } catch (Throwable $e) {
            $this->respondJson([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
