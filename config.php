<?php   
/**
* config class for storing constants
*/
class config {

	//db config
	protected $username = '';
	protected $password = '';
	protected $hostname = '';
	protected $dbname 	= '';
	public $db = '';
	public $url= '';
	public $resHtml = '';
	public $channelName = '';
	function __construct($channel_name){
		  require_once 'vendor/autoload.php';
		  $dotenv = new Dotenv\Dotenv(__DIR__);
		  $dotenv->load();
		  $this->hostname = getenv('DB_HOST');
		  $this->username = getenv('DB_USERNAME');
		  $this->password = getenv('DB_PASSWORD');
		  $this->dbname   = getenv('DB_DATABASE');
		  $this->channelName = $channel_name;
		  $this->db = mysqli_connect($this->hostname,$this->username,$this->password,$this->dbname);
	}
	function http($url){
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		  $data = curl_exec($ch);
		  curl_close($ch);
		  return $data;
	}
	function str_replace_gizmodization($str){ 
	    $data =  str_replace('dainik','',$str);
	    $data =  str_replace('Dainik','',$data);
	    $data =  str_replace('bhaskar','',$data);
	    $data =  str_replace('Bhaskar','',$data);
	    return $data."".$this->channelName." Channel. Subscribe to our channel ".$this->channelName." for latest updates";
	}

}


/**
 * 
 */
class Processing extends config
{
	public $save_to;

	function __construct($channel_name,$save_to)
	{
		$this->save_to = $save_to;
		parent::__construct($channel_name);  	
	}
	function processAllPost(){
		$save_loc = $this->save_to;
		$res = mysqli_query($this->db,"SELECT pid FROM dainik_bhasker WHERE processed=0");
		while($data = mysqli_fetch_assoc($res)) {
			$i = $data['pid'];
			echo PHP_EOL." Processing post -> $i ".PHP_EOL;
			exec("bash tts \"$save_loc/$i\" >  /dev/null 2>&1 ");
		}
		
	}
}

