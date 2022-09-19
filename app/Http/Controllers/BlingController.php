<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlingController extends Controller
{
    public function blingApi(): JsonResponse
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://bling.com.br/Api/v2/produtos/json/?apikey=367f9bd70c7404e8ff330c61fb053eefb49f35cc4a8d034ea114cb860335f561b893a1f1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        $var = json_decode($response);

        curl_close($curl);

        return new JsonResponse($var, 200);
    }
    /////////pega todos os produtos cadastrados no bling e os cria no portal
    function executeGetProducts($url, $apikey)
        {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $url . '&apikey=' . $apikey);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($curl_handle);
            curl_close($curl_handle);
            return $response;
        }
    public function getProducts()
    {
        $apikey = "367f9bd70c7404e8ff330c61fb053eefb49f35cc4a8d034ea114cb860335f561b893a1f1";
        $outputType = "json";
        $url = 'https://bling.com.br/Api/v2/produtos/' . $outputType;
        $retorno = $this->executeGetProducts($url, $apikey);

        echo $retorno;
    }

    public function createProducts($post) {
        $json = json_decode($post);

        $p = Products::Where([
            ["id", "=", $json->post->produtos->produto->id]
        ])->get();

        if (empty($p) === false) {
            $response = [
                'Code' => '400',
                'Type' => 'error',
                'Message' => 'Produto já cadastrado!',
                'Data' => null
            ];
            return new JsonResponse($response, 400);
        }else {
            $product = new Products();

            $product->id = $json->post->produtos->produto->id;
            $product->codigo = $json->post->produtos->produto->codigo;
            $product->descricao = $json->post->produtos->produto->descricao;
            $product->teste = $json->post->produtos->produto->teste;
            $product->tipo = $json->post->produtos->produto->tipo;
            $product->situacao = $json->post->produtos->produto->situacao;
            $product->unidade = $json->post->produtos->produto->unidade;
            $product->preco = $json->post->produtos->produto->preco;
            $product->precoCusto = $json->post->produtos->produto->precoCusto;
            $product->descricaoCurta = $json->post->produtos->produto->descricaoCurta;
            $product->descricaoComplementar = $json->post->produtos->produto->descricaoComplementar;
            $product->dataInclusao = $json->post->produtos->produto->dataInclusao;
            $product->dataAlteracao = $json->post->produtos->produto->dataAlteracao;
            $product->imageThumbnail = $json->post->produtos->produto->imageThumbnail;
            $product->urlVideo = $json->post->produtos->produto->urlVideo;
            $product->nomeFornecedor = $json->post->produtos->produto->nomeFornecedor;
            $product->codigoFabricante = $json->post->produtos->produto->codigoFabricante;
            $product->marca = $json->post->produtos->produto->marca;
            $product->class_fiscal = $json->post->produtos->produto->class_fiscal;
            $product->cest = $json->post->produtos->produto->cest;
            $product->origem = $json->post->produtos->produto->origem;
            $product->idGrupoProduto = $json->post->produtos->produto->idGrupoProduto;
            $product->linkExterno = $json->post->produtos->produto->linkExterno;
            $product->observacoes = $json->post->produtos->produto->observacoes;
            $product->grupoProduto = $json->post->produtos->produto->grupoProduto;
            $product->garantia = $json->post->produtos->produto->garantia;
            $product->descricaoFornecedor = $json->post->produtos->produto->descricaoFornecedor;
            $product->categoria->id = $json->post->produtos->produto->categoria->id;
            $product->categoria->descricao = $json->post->produtos->produto->categoria->descricao;
            $product->pesoLiq = $json->post->produtos->produto->pesoLiq;
            $product->pesoBruto = $json->post->produtos->produto->pesoBruto;
            $product->estoqueMinimo = $json->post->produtos->produto->estoqueMinimo;
            $product->estoqueMaximo = $json->post->produtos->produto->estoqueMaximo;
            $product->gtin = $json->post->produtos->produto->gtin;
            $product->gtinEmbalagem = $json->post->produtos->produto->gtinEmbalagem;
            $product->larguraProduto = $json->post->produtos->produto->larguraProduto;
            $product->alturaProduto = $json->post->produtos->produto->alturaProduto;
            $product->profundidadeProduto = $json->post->produtos->produto->profundidadeProduto;
            $product->unidadeMedida = $json->post->produtos->produto->unidadeMedida;
            $product->itensPorCaixa = $json->post->produtos->produto->itensPorCaixa;
            $product->volumes = $json->post->produtos->produto->volumes;
            $product->localizacao = $json->post->produtos->produto->localizacao;
            $product->crossdocking = $json->post->produtos->produto->crossdocking;
            $product->condicao = $json->post->produtos->produto->condicao;
            $product->freteGratis = $json->post->produtos->produto->freteGratis;
            $product->producao = $json->post->produtos->produto->producao;
            $product->dataValidade = $json->post->produtos->produto->dataValidade;
            $product->spedTipoItem = $json->post->produtos->produto->spedTipoItem;

            try {
                $product->save();
            } catch (\Exception $th) {
                $response = [
                    'Code' => '400',
                    'Type' => 'error',
                    'Message' => 'Dados Inválidos!',
                    'Data' => null
                ];
                return new JsonResponse($response, 400);
            }

            $response = [
                'Code' => '200',
                'Type' => 'Success',
                'Message' => 'Produtos Criados!',
                'Data' => null
            ];
            return new JsonResponse($response, 200);
        }
    }

    function executeGetProduct($url, $apikey)
        {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $url . '&apikey=' . $apikey);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($curl_handle);
            curl_close($curl_handle);
            return $response;
        }
    public function getCreateProductById($codigo)
    {   
        $code = $codigo;
        $apikey = "367f9bd70c7404e8ff330c61fb053eefb49f35cc4a8d034ea114cb860335f561b893a1f1";
        $outputType = "json";
        $url = 'https://bling.com.br/Api/v2/produto/' . $code . '/' . $outputType;
        $retorno = $this->executeGetProduct($url, $apikey);

        echo $retorno;
    }
    /////////////////////////tem call back
    public function inventoryCallback(Request $request): JsonResponse
    {   
        foreach ($request->retorno->estoques as $e) {
            $inventory = Products::findOrFail($e->estoque->id);
        }
        
        print_r($request->data);
        exit;
        
        try {
            $inventory->update([
                "estoqueAtual" => $e->estoque->estoqueAtual
            ]);
        } catch (\Throwable $th) {
            $response = [
                'Code' => '400',
                'Type' => 'Error',
                'Message' => 'Erro ao Atualizar Estoque',
                "Data" => null
            ];
            return new JsonResponse($response, 400);
        }

        $response = [
            'Code' => '200',
            'Type' => 'Success',
            'Message' => 'Estoque Atualizado',
            "Data" => $inventory
        ];

        return new JsonResponse($response, 200);
        
    }
    //////////////////////nao tem callback (precisa de fila)
    public function productCallback(Request $request): JsonResponse
    {
        $product = Products::findOrFail($request->product->id);

        if ($product->situacao === "Inativo") {
            $product->delete();

            $response = [
                'Code' => '200',
                'Type' => 'Success',
                'Message' => 'Produto Deletado!',
                "Data" => null

            ];

            return new JsonResponse($response, 200);
        } else {
            try {
                $product->update([
                    'id' => $request->id,
                    'codigo' => $request->codigo,
                    'descricao' => $request->descricao,
                    'teste' => $request->teste,
                    'tipo' => $request->tipo,
                    'situacao' => $request->situacao,
                    'unidade' => $request->unidade,
                    'preco' => $request->preco,
                    'precoCusto' => $request->precoCusto,
                    'descricaoCurta' => $request->descricaoCurta,
                    'descricaoComplementar' => $request->descricaoComplementar,
                    'dataInclusao' => $request->dataInclusao,
                    'dataAlteracao' => $request->dataAlteracao,
                    'imageThumbnail' => $request->imageThumbnail,
                    'urlVideo' => $request->urlVideo,
                    'nomeFornecedor' => $request->nomeFornecedor,
                    'codigoFabricante' => $request->codigoFabricante,
                    'marca' => $request->marca,
                    'class_fiscal' => $request->class_fiscal,
                    'cest' => $request->cest,
                    'origem' => $request->origem,
                    'idGrupoProduto' => $request->idGrupoProduto,
                    'linkExterno' => $request->linkExterno,
                    'observacoes' => $request->observacoes,
                    'grupoProduto' => $request->grupoProduto,
                    'garantia' => $request->garantia,
                    'descricaoFornecedor' => $request->descricaoFornecedor,
                    'categoria' => $request->categoria,
                    'pesoLiq' => $request->pesoLiq,
                    'pesoBruto' => $request->pesoBruto,
                    'estoqueMinimo' => $request->estoqueMinimo,
                    'estoqueMaximo' => $request->estoqueMaximo,
                    'gtin' => $request->gtin,
                    'gtinEmbalagem' => $request->gtinEmbalagem,
                    'larguraProduto' => $request->larguraProduto,
                    'alturaProduto' => $request->alturaProduto,
                    'profundidadeProduto' => $request->profundidadeProduto,
                    'unidadeMedida' => $request->unidadeMedida,
                    'itensPorCaixa' => $request->itensPorCaixa,
                    'volumes' => $request->volumes,
                    'localizacao' => $request->localizacao,
                    'crossdocking' => $request->crossdocking,
                    'condicao' => $request->condicao,
                    'freteGratis' => $request->freteGratis,
                    'producao' => $request->producao,
                    'dataValidade' => $request->dataValidade,
                    'spedTipoItem' => $request->spedTipoItem
                ]);
            } catch (\Throwable $th) {
                $response = [
                    'Code' => '400',
                    'Type' => 'Error',
                    'Message' => 'Dados inválidos!',
                    "Data" => null

                ];

                return new JsonResponse($response, 200);
            }

            $response = [
                'Code' => '200',
                'Type' => 'Success',
                'Message' => 'Produto Atualizado!',
                "Data" => null

            ];

            return new JsonResponse($response, 200);
        }
    }
}
