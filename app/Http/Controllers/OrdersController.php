<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use DOMDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SimpleXMLElement;

class OrdersController extends Controller
{
    function array2xml($array, $xml = false)
    {
        if ($xml === false) {
            $xml = new SimpleXMLElement('<result/>');
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->array2xml($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
            }
        }

        return $xml->asXML();
    }
    function executeSendOrder($url, $data)
    {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_POST, count($data));
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }
    //pedido no portal gerando pedido no bling
    public function createOrder(Request $request): JsonResponse
    {
        $result = $request->pedido;
        $order = new Orders;

        $order->cliente = $result["cliente"];
        $order->volume = $result["volume"];
        $order->item = $result["item"];
        $order->parcela = $result["parcela"];

        try {
            //$order->save();
        } catch (\Throwable $th) {
            $response = [
                'Code' => '400',
                'Type' => 'error',
                'Message' => 'Dados Inválidos!',
                'Data' => null
            ];
            return new JsonResponse($response, 400);
        }

        $json = json_decode($order, true);

        $xml = $this->array2xml($json, false);

        $url = 'https://bling.com.br/Api/v2/pedido/json/';
        $posts = array(
            "apikey" => "367f9bd70c7404e8ff330c61fb053eefb49f35cc4a8d034ea114cb860335f561b893a1f1",
            "xml" => rawurlencode($xml)
        );

        $retorno = $this->executeSendOrder($url, $posts);

        $response = [
            'Code' => '200',
            'Type' => 'Success',
            'Message' => 'Pedido Criado!',
            'Data' => $retorno
        ];
        return new JsonResponse($response, 200);
    }

    function executeUpdateOrder($url, $data)
    {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl_handle, CURLOPT_POST, count($data));
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $response;
    }
    public function updateOrder(Request $request)
    {
        $order = Orders::findOrFail($request->pedido["numero"]);
        try {
            $order->update([
                "status" => $request->pedido->situacao
            ]);
        } catch (\Throwable $th) {
            echo "Erro ao salvar as alterações.";
        }

        if ($order->status != $request->pedido["status"]) {
            switch ($order->status) {
                case 'Cancelado':
                    $id = 12;
                    break;
                case 'Verificado':
                    $id = 24;
                break;
            }

            //id usado para teste:
            $id = 12;
            $url = 'https://bling.com.br/Api/v2/pedido/' . $request->pedido["numero"] . '/json';

            $content = '<?xml version="1.0" encoding="UTF-8"?>';
            $content .= '<pedido>';
            $content .= '<idSituacao>' . $id . '</idSituacao>';
            $content .= '</pedido>';

            $posts = array(
                'apikey' => '367f9bd70c7404e8ff330c61fb053eefb49f35cc4a8d034ea114cb860335f561b893a1f1',
                'xml' => rawurlencode($content)
            );
            $retorno = $this->executeUpdateOrder($url, $posts);
            echo $retorno;
        } else {
            return "O status ja é esse!";
        }
    }
    
    public function orderCallback(Request $request) 
    {   
        $result = $request->data["retorno"]["pedidos"][0];
        $res = $result["pedido"];

        print_r($res);
        exit;

        $order = Orders::findOrFail($res["numero"]);

        switch ($res["status"]) {
            case 'Em aberto':
                $order->update([
                    "idsituacao" => 6,
                    "situacao" => "Em aberto"
                ]);
                break;
            case 'Atendido':
                $order->update([
                    "idsituacao" => 9,
                    "situacao" => "Atendido"
                ]);
                break;
            case 'Cancelado':
                $order->update([
                    "idsituacao" => 12,
                    "situacao" => "Cancelado"
                ]);
                break;
            case 'Em andamento':
                $order->update([
                    "idsituacao" => 15,
                    "situacao" => "Em andamento"
                ]);
                break;
            case 'Venda agenciada':
                $order->update([
                    "idsituacao" => 18,
                    "situacao" => "Venda agenciada"
                ]);
                break;
            case 'Em digitação':
                $order->update([
                    "idsituacao" => 21,
                    "situacao" => "Em digitação"
                ]);
                break;
            case 'Verificado':
                $order->update([
                    "idsituacao" => 24,
                    "situacao" => "Verificado"
                ]);
                break;
            default:
                echo "Impossível alterar a situação do pedido";
            break;
        }

        try {
            $order->update();
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

}
