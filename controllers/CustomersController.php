<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\customer\CustomerRecord;
use app\models\customer\PhoneRecord;
use app\models\customer\Customer;
use app\models\customer\Phone;
use yii\data\ArrayDataProvider;
use Yii;

class CustomersController extends Controller {

	public function actionAdd()
	{
		$customer 	= new CustomerRecord();
		$phone 		= new PhoneRecord();		

		if ($this->load($customer, $phone, $_POST)) 
		{			
			$this->store($this->makeCustomer($customer, $phone));
			return $this->redirect('customers');
		}

		return $this->render('add', compact('phone', 'customer'));	
	}

	private function load(CustomerRecord $customer, PhoneRecord $phone, array $post)
	{
		return $customer->load($post) 
			and $phone->load($post)
			and $customer->validate()
			and $phone->validate(['number']);
	}

	public function actionIndex()
	{
		$records = $this->findRecordsByQuery();		
		return $this->render( 'index', compact( 'records' ) );
	}

	public function actionQuery()
	{
		return $this->render('query');
	}

	private function findRecordsByQuery()
	{
		$number = Yii::$app->request->get('phone_number');
		$records = $this->getRecordsByPhoneNumber($number);
		$dataProvider = $this->wrapIntoDataProvider($records);
		return $dataProvider;
	}

	private function wrapIntoDataProvider($data)
	{
		return new ArrayDataProvider([
			'allModels' => $data,
			'pagination' => false
		]);
	}

	private function getRecordsByPhoneNumber($number)
	{
		$phoneRecord = PhoneRecord::findOne(['number' => $number]);
		if ( ! $phoneRecord) {
			return [];
		}

		$customerRecord = CustomerRecord::findOne($phoneRecord->customer_id);
		if ( ! $customerRecord) {
			return [];
		}

		return [$this->makeCustomer($customerRecord, $phoneRecord)];
	}

	private function store(Customer $customer)
	{
		$customerRecord = new CustomerRecord();
		$customerRecord->name = $customer->name;
		$customerRecord->birth_date = $customer->birth_date->format("Y-m-d");
		$customerRecord->notes = $customer->notes;
		$customerRecord->save();

		foreach ($customer->phones as $phone) 
		{
			$phoneRecord = new PhoneRecord();
			$phoneRecord->number = $phone->number;
			$phoneRecord->customer_id = $customerRecord->id;
			$phoneRecord->save();
		}
	}

	private function makeCustomer(CustomerRecord $customerRecord, PhoneRecord $phoneRecord)
	{		
		$name = $customerRecord->name;
		$birthDate = new \DateTime($customerRecord->birth_date);

		$customer = new Customer($name, $birthDate);
		$customer->notes = $customerRecord->notes;
		$customer->phones[] = new Phone($phoneRecord->number);

		return $customer;
	}

}