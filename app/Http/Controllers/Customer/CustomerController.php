<?php

namespace App\Http\Controllers\Customer;

use App\Dto\Customer\CustomerDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Services\Customer\CustomerService;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }
    /**
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Cadastrar um novo cliente",
     *     tags={"Customers"},
     *     security={{"meu_token": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Informe o nome e e‑mail para realizar o cadastro do cliente.",
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string", example="Maria Oliveira"),
     *             @OA\Property(property="email", type="string", example="maria.oliveira@exemplo.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente cadastrado com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=42),
     *                 @OA\Property(property="name", type="string", example="Maria Oliveira"),
     *                 @OA\Property(property="email", type="string", example="maria.oliveira@exemplo.com"),
     *                 @OA\Property(property="created_at", type="string", example="2025-06-25 10:45:12"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-06-25 10:45:12")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alguns campos não foram preenchidos corretamente."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="O nome do cliente é obrigatório.")
     *                 ),
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="Informe um e‑mail válido e não utilizado anteriormente.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreCustomerRequest $request): CustomerResource
    {
        $dto = CustomerDto::fromArray($request->validated());

        $customer = $this->customerService->create($dto);

        return new CustomerResource($customer);
    }

    /**
     * @OA\Put(
     *     path="/api/customers/{id}",
     *     summary="Atualizar dados do cliente",
     *     tags={"Customers"},
     *     security={{"meu_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente a ser atualizado",
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Informe o nome e e‑mail para atualizar o cliente.",
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", example="joao.silva@exemplo.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente atualizado com sucesso.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=42),
     *                 @OA\Property(property="name", type="string", example="João Silva"),
     *                 @OA\Property(property="email", type="string", example="joao.silva@exemplo.com"),
     *                 @OA\Property(property="created_at", type="string", example="2025-06-25 10:45:12"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-06-25 11:22:53")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alguns campos não foram preenchidos corretamente."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="O nome do cliente é obrigatório.")
     *                 ),
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="Informe um e‑mail válido e não utilizado anteriormente.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateCustomerRequest $request, int $id): CustomerResource
    {
        $dto = CustomerDto::fromArray($request->validated());

        $customer = $this->customerService->update($id, $dto);

        return new CustomerResource($customer);
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     summary="Exibir dados de um cliente específico",
     *     tags={"Customers"},
     *     security={{"meu_token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do cliente a ser exibido",
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=42),
     *                 @OA\Property(property="name", type="string", example="João Marinho"),
     *                 @OA\Property(property="email", type="string", example="joao.marinho@exemplo.com"),
     *                 @OA\Property(property="created_at", type="string", example="2025-06-25 10:45:12"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-06-25 10:45:12")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado."
     *     )
     * )
     */
    public function show(int $id): CustomerResource
    {
        $customer = $this->customerService->find($id);

        return new CustomerResource($customer);
    }
}
