<?

class Sender {
	public $to;
	public $subject;
	public $message;
	public $fields = [];
	public $redirect;
	public $spamFilter;

	public function __construct()
	{
		header("Content-Type: application/json");
	}

	public function isValidRequest()
	{
		return ($_SERVER['REQUEST_METHOD'] === 'POST');
	}

	public function to($to)
	{
		$this->to = $to;
	}


	public function subject($subject)
	{
		$this->subject = $subject;
	}


	public function redirect($redirect)
	{
		$this->redirect = $redirect;
		header('Location: '.$this->redirect);
	}

	public function spamFilter($count = 0)
	{
		if(!$this->isValidRequest()) {
			$this->error('badRequest');
		}
		if($count > 0) {
			require_once(__DIR__.DIRECTORY_SEPARATOR.'SpamFilter.php');
			$this->spamFilter = new SpamFilter($count);
		}
	}


	public function addField($fieldName = '', $post_key, $isRequired = false)
	{
		$value = trim($_POST[$post_key]);
		if(!$value && $isRequired) {
			$errorPayload = [
				'field' =>  $post_key,
				'reason' => 'required'
			];
			$this->error('validation', $errorPayload);
		}
		$this->fields[] = ['name' => $fieldName, 'value' => $value];
	}

	public function addText($value = ''){
		$this->fields[] = ['value' => $value];
	}

	public function error($error = null, $payLoad = null)
	{
		$response = [
			'success' => 0,
			'errorReason' => $error,
			'errorPayload' => $payLoad
		];
		echo json_encode($response);
		die();
	}

	public function createMessage()
	{
		foreach($this->fields as $field){
			if(isset($field['name'])) {
				$message .= $field['name'].': '.$field['value']."\r\n";
			} else {
				$message .= $field['value']."\r\n";
			}
		}
		$this->message = $message;
	}

	public function beforeSend()
	{
		$this->createMessage();
		if($this->spamFilter && $this->spamFilter->hasBan()) {
			$diff = (new DateTime('NOW'))->diff((new DateTime('now +1 day midnight')));
			$errorPayload = [
				'unbanAfter' => [
					'hours' => $diff->format('%H'),
					'minutes' => $diff->format('%i'),
					'seconds' => $diff->format('%s')
				]
			];
			$this->error('ban',  $errorPayload);
		}
	}

	public function afterSend()
	{
		if($this->spamFilter) {
			$this->spamFilter->increment();
		}
		$response = ['success' => 1];
		echo json_encode($response);
	}

	public function send()
	{
		$this->beforeSend();
		mail($this->to, $this->subject, $this->message);
		$this->afterSend();
	}
}
