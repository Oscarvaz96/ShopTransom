<?php
namespace Transom\Pakke\Model;

use Zend\Http\Client;
use \Magento\Framework\Json\Helper\Data;
use Transom\Pakke\Helper\ConfigFunctions;
use Magento\Framework\Serialize\SerializerInterface;
use \Transom\Pakke\Logger\Logger;

class Connect
{
	protected $zendClient;
	protected $serializer;
	protected $_jsonHelper;
	protected $configFunctions;
	protected $logger;
	
	public function __construct(Client $httpClient, ConfigFunctions $configFunctions, Data $jsonHelper, SerializerInterface $serializer,Logger $logger)
    {
        $this->zendClient = $httpClient;
		$this->_jsonHelper = $jsonHelper;
		$this->serializer = $serializer;
		$this->configFunctions = $configFunctions;
		$this->logger = $logger;
    }

	public function getConnection($url, $method){

		if($this->configFunctions->getDebugger() == true){
			$this->logger->info("Debugger: Url to send = ".$url);
			$this->logger->info("Debugger: Method to send = ".$method);
		}
		
		try {
			$this->zendClient->setUri($url);
			$this->zendClient->setMethod($method);
			$this->zendClient->setOptions(['timeout' => 60]);  
			$this->zendClient->setHeaders([
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => $this->configFunctions->getApiKey(),
			]);
			$this->zendClient->send();
			$response = $this->zendClient->getResponse();
			$responseBody = $response->getBody();
			$decodedData =  $this->_jsonHelper->jsonDecode($responseBody);

			if($this->configFunctions->getLogger() == true){
				$this->logger->info("Logger: Pakke respuesta = ".$responseBody);
			}
            
            return $decodedData;

		} catch (\Throwable $th) {
            print($th->getMessage());
            return $th->getMessage();
		}
    }
	
	public function createConnection($url, $method, $body){

		if($this->configFunctions->getDebugger() == true){
			$this->logger->info("Debugger: Url to send = ".$url);
			$this->logger->info("Debugger: Method to send = ".$method);
			$this->logger->info("Debugger: Body to send = ".$this->_jsonHelper->jsonEncode($body));
		}

		try {
			$this->zendClient->setUri($url);
			$this->zendClient->setMethod($method);
			$this->zendClient->setOptions(['timeout' => 60]);  
			$this->zendClient->setHeaders([
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => $this->configFunctions->getApiKey(),
			]);
			$encodedData = $this->_jsonHelper->jsonEncode($body);
			$this->zendClient->setRawBody($encodedData)->send();
			$responseBody = $this->zendClient->getResponse()->getBody();
			$decodedData =  $this->_jsonHelper->jsonDecode($responseBody);
			
			if($this->configFunctions->getLogger() == true){
				$this->logger->info("Logger: Pakke respuesta = ".$responseBody);
			}

            return $decodedData;

		} catch (\Throwable $th) {
            print($th->getMessage());
            return $th->getMessage();
		}
    }
}