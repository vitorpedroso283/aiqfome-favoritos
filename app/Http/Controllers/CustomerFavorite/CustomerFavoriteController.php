<?php

namespace App\Http\Controllers\CustomerFavorite;

use App\Dto\CustomerFavorite\StoreCustomerFavoriteDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerFavorite\StoreCustomerFavoriteRequest;
use App\Http\Requests\ListCustomerFavoritesRequest;
use App\Http\Resources\CustomerFavorite\CustomerFavoriteResource;
use App\Models\Customer;
use App\Services\CustomerFavorite\CustomerFavoriteService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomerFavoriteController extends Controller
{

    protected CustomerFavoriteService $customerFavoriteService;

    public function __construct(CustomerFavoriteService $customerFavoriteService)
    {
        $this->customerFavoriteService = $customerFavoriteService;
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{customer_id}/favorites",
     *     summary="Listar todos os favoritos de um cliente",
     *     tags={"Favorites"},
     *     security={{"meu_token": {}}},
     *     @OA\Parameter(
     *         name="customer_id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Quantidade de registros por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Campo para ordenação",
     *         required=false,
     *         @OA\Schema(type="string", example="product_id")
     *     ),
     *     @OA\Parameter(
     *         name="order_dir",
     *         in="query",
     *         description="Direção da ordenação (asc|desc)",
     *         required=false,
     *         @OA\Schema(type="string", example="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listagem de favoritos carregada com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                      @OA\Property(property="product_id", type="integer", example=1),
     *                      @OA\Property(property="title", type="string", example="Produto Teste"),
     *                      @OA\Property(property="price", type="number", example=99.99),
     *                      @OA\Property(property="image", type="string", example="https://exemplo.com/image.png"),
     *                      @OA\Property(property="review", type="object",
     *                           @OA\Property(property="rate", type="number", example=4.5),
     *                           @OA\Property(property="count", type="integer", example=200)
     *                      )
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string", example="http://localhost/api/customers/1/favorites?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://localhost/api/customers/1/favorites?page=5"),
     *                 @OA\Property(property="prev", type="string", example=null),
     *                 @OA\Property(property="next", type="string", example="http://localhost/api/customers/1/favorites?page=2")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/customers/1/favorites"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     )
     * )
     */
    public function index(int $customer_id, ListCustomerFavoritesRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $perPage = $validated['per_page'] ?? 10;
        $orderBy = $validated['order_by'] ?? 'product_id';
        $orderDir = $validated['order_dir'] ?? 'asc';

        $favorites = $this->customerFavoriteService->all($customer_id, $perPage, $orderBy, $orderDir);

        return CustomerFavoriteResource::collection($favorites);
    }

    /**
     * @OA\Post(
     *     path="/api/customers/{customer}/favorites",
     *     summary="Adiciona um produto aos favoritos do cliente",
     *     tags={"Favorites"},
     *     security={{"meu_token": {}}},
     *     @OA\Parameter(
     *         name="customer",
     *         in="path",
     *         required=true,
     *         description="ID do cliente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Informe o ID do produto para adicionar aos favoritos.",
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto adicionado aos favoritos com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Produto Teste"),
     *                 @OA\Property(property="price", type="number", example=99.99),
     *                 @OA\Property(property="image", type="string", example="https://exemplo.com/image.png"),
     *                 @OA\Property(property="review", type="object",
     *                     @OA\Property(property="rate", type="number", example=4.5),
     *                     @OA\Property(property="count", type="integer", example=200)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Cliente não encontrado."),
     *     @OA\Response(response=422, description="Erro de validação.")
     * )
     */
    public function store(int $customer, StoreCustomerFavoriteRequest $request): JsonResponse|CustomerFavoriteResource
    {

        if (!Customer::find($customer)) {
            return response()->json([
                'error' => 'Customer not found',
            ], 404);
        }

        $dto = StoreCustomerFavoriteDto::fromArray($request->validated());

        $favorite = $this->customerFavoriteService->add($customer, $dto);

        return new CustomerFavoriteResource($favorite);
    }
}
