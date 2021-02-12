<?

class SpamFilter {
	public $dbFileName = 'spam_db.json';
	public $dbFilePath;
	public $db;
	public $maxSend;
	public $ip;

	public function __construct($maxSend = 1)
	{
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->maxSend = $maxSend;
		$this->dbFilePath = __DIR__.DIRECTORY_SEPARATOR.$this->dbFileName;
		$this->db = $this->getDB();
	}

	public function getDB()
	{
		if (file_exists($this->dbFilePath)) {
	    	$db = file_get_contents($this->dbFilePath);
	    	$db = json_decode($db, true);
	    	if(isset($db)) {
	    		return $db;
	    	}
		}
		return [];
	}

	public function hasBan()
	{
		if(!isset($this->db[$this->ip]['count'])) {
			return false;
		}

		return ($this->db[$this->ip]['count'] >= $this->maxSend && $this->db[$this->ip]['date'] === date("Y-m-d")) ? true : false;
	}

	public function increment()
	{
		if(
			!isset($this->db[$this->ip]['count']) ||
			isset($this->db[$this->ip]['date']) && 
			$this->db[$this->ip]['date'] !== date("Y-m-d")
		) {
			$this->db[$this->ip] = ['count' => 1];
		} else {
			++$this->db[$this->ip]['count'];
		}
		$this->db[$this->ip]['date'] = date("Y-m-d");
		file_put_contents($this->dbFilePath, json_encode($this->db));
	}



}
