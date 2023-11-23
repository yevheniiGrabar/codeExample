<?php

namespace App\Http\Controllers;

use App\Models\LocationProduct;
use App\Models\Product;
use App\Models\User;
use App\Traits\CurrentCompany;
use http\Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

/**
 * @group PowerOffice
 *
 * Endpoints for managing PowerOffice
 */
class PowerOfficeController extends Controller
{
    public const CONTENT_TYPE = 'application/json';
    public const  PRODUCT_GROUP_API_ENDPOINT = 'https://api-demo.poweroffice.net/ProductGroup/';
    public const PRODUCT_API_ENDPOINT = 'https://api-demo.poweroffice.net/Product/';

    public function getAuthUser(): Model|Collection|Builder|array|null
    {
        $id = Auth::id();

        return User::query()->find($id);
    }

    public function sendRequest()
    {
        $user = $this->getAuthUser();

        return $this->getToken($user->app_key, $user->client_key);
    }

    public function createProductGroup()
    {
        $token = $this->sendRequest();

        try {
            $response = Http::withHeaders(
                [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ]
            )->post(
                'https://api-demo.poweroffice.net/ProductGroup/',
                [
                    "name" => "Test",
                    "description" => "Test",
                    "type" => 0,
                    "unit" => "metres"
                ]
            );

            if ($response->status() == 200) {
                echo $response->body();
            } else {
                echo 'Unexpected HTTP status: ' . $response->status() . ' ' . $response->reason();
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function prepareDataForCreateNewProduct()
    {
        $token = $this->sendRequest();

        try {
            $response = Http::withHeaders(
                [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ]
            )->get(
                'https://api-demo.poweroffice.net/ProductGroup/',
                []
            );

            $groups = $response->body();
//            dd($groups);
            $groups = json_decode($groups, true);
            $groupId = '';
            if (is_array($groups)) {
                foreach ($groups as $data) {
                    if (is_array($data)) {
                        foreach ($data as $group) {
                            if ($group['name'] == 'Cables') {
                                // Выполните необходимые действия для найденной записи 'Cables'
                                $groupId = $group['id'];
                                $groupName = $group['name'];
                                // и так далее
                                break; // Выход из внутреннего цикла, если запись найдена
                            }
                        }
                    }
                }
            }

//            $currentCompany = CurrentCompany::getDefaultCompany();
//            $products = Product::query()->where('company_id', $currentCompany->company_id)->get();
//
//            foreach ($products as $product) {
//                $locationProducts = LocationProduct::query()->where('product_id', $product->id)->get();
//
//                foreach ($locationProducts as $locationProduct) {
//                    $existingProduct = Http::withHeaders(
//                        [
//                            'Content-Type' => 'application/json',
//                            'Authorization' => 'Bearer ' . $token,
//                        ]
//                    )->get(self::PRODUCT_API_ENDPOINT, ['name' => $product->name]);
//
//                    if ($existingProduct->status() === 200) {
//                        // Продукт уже существует, пропускаем создание
//                        continue;
//                    }
//
//                    $response = Http::withHeaders(
//                        [
//                            'Content-Type' => 'application/json',
//                            'Authorization' => 'Bearer ' . $token,
//                        ]
//                    )->post(
//                        self::PRODUCT_API_ENDPOINT,
//                        [
//                            'name' => $product->name,
//                            'description' => $product->description,
//                            'productGroupId' => $groupId,
//                            'type' => 0,
//                            'unitOfMeasureCode' => 5,
//                            'costPrice' => $product->cost_price,
//                            'salesPrice' => $product->sale_price,
//                            'productsOnHand' => $locationProduct->in_stock,
//                            'isActive' => true
//                        ]
//                    );
//                }
//            }
            $currentCompany = CurrentCompany::getDefaultCompany();
            $products = Product::query()->where('company_id', $currentCompany->company_id)->get();

            foreach ($products as $product) {
                $locationProducts = LocationProduct::query()->where('product_id', $product->id)->get();
//                dd($locationProducts);
                foreach ($locationProducts as $locationProduct) {
                    $response = Http::withHeaders(
                        [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $token,
                        ]
                    )
                        ->post(
                            self::PRODUCT_API_ENDPOINT,
                            [
                                'name' => $product->name,
                                'description' => $product->description,
                                'productGroupId' => $groupId,
                                'type' => 0, // what this ?
                                'unitOfMeasureCode' => 5, // & what this?
                                'costPrice' => $product->cost_price,
                                'salesPrice' => $product->sale_price,
                                'productsOnHand' => $locationProduct->in_stock,
                                'isActive' => true
                            ]
                        );
                }
            }

            if ($response->status() == 200) {
                echo $response->body();
            } else {
                echo 'Unexpected HTTP status: ' . $response->status() . ' ' . $response->reason();
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }


    public function getAllProducts()
    {
        $token = $this->sendRequest();

        try {
            $response = Http::withHeaders(
                [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ]
            )->get(
                'https://api-demo.poweroffice.net/Product/',
                []
            );

            if ($response->status() == 200) {
               return $response->body();
            } else {
                echo 'Unexpected HTTP status: ' . $response->status() . ' ' . $response->reason();
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

//    public function createProduct($token, $quantity = null, $group = null)
//    {
//        $products = Product::query()->limit(10)->get();
//
//        foreach ($products as $product) {
//            $response = Http::withHeaders(
//                [
//                    'Content-Type' => self::CONTENT_TYPE,
//                    'Authorization' => $token,
//                ]
//            )
//                ->post(
//                    self::PRODUCT_API_ENDPOINT,
//                    [
//                        'name' => $product->name,
//                        'description' => $product->description,
//                        'productGroupId' => $group,
//                        'type' => 0, // what this ?
//                        'unitOfMeasureCode' => 5, // & what this?
//                        'costPrice' => $product->cost_price,
//                        'salesPrice' => $product->sale_price,
//                        'productsOnHand' => $quantity,
//                        'isActive' => true
//                    ]
//                );
//        }
//
//        if ($response->ok()) {
//            echo $response->body();
//        } else {
//            echo 'Unexpected HTTP status: ' . $response->status() . ' ' . $response->reason();
//        }
//    }
//

    /**
     * @param string $login
     * @param string $password
     * @return mixed
     */
    public function getToken(string $login, string $password): mixed
    {
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api-demo.poweroffice.net/OAuth/Token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
                CURLOPT_USERPWD => "$login:$password"
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        $jsonResponse = json_decode($response);

        return $jsonResponse->access_token;
    }

    public function savePowerOfficeCredentialsToCurrentUser(Request $request): JsonResponse
    {
        $id = Auth::id();
        $user = User::query()->find($id);

        $user->update($request->all());

        return new JsonResponse($user);
    }
}
