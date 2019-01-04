<?
namespace Applications;

class CSVParser
{
	private $result = null;

	public function __construct( $file )
	{
		$row 		= 1;
		$row_heads 	= array();
		$items 		= array();

		if (($handle = fopen( $file, "r")) !== FALSE)
		{
		    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
		    {
		        $num 	= count($data);
		        $rowi 	= array();

		        for ($c=0; $c < $num; $c++)
		        {
		        	if($row == 1) {
			    		$row_heads[] = $data[$c];
			    	} else {
			    		$rowi[$row_heads[$c]] = $data[$c];
			    	}
		        }

		        if( $rowi ) {
		        	$items[] = $rowi;
		        }

		        unset($rowi);

		        $row++;
		    }

		    fclose($handle);

		    $this->result = $items;

		    unset($items);
		}
	}

	public function getResult( $deep = false )
	{
		return $this->result;
	}

	public function countItems()
	{
		return (int)count($this->result);
	}
}
?>
