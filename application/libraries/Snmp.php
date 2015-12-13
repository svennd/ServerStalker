<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

# based on http://www.juros.hr/data/Rrd.php.txt

# create class
class Rrd {

	# create
	public function create($location, $options) {	
		print_r($options);
		$result = rrd_create($location, $options);
		if ($result) {
			print rrd_error();
		    return false;
		}
		return true;	
	}
	

	function update($location, $values) {
		
		$input_string = 'N';
		foreach ($values as $value) {	
			$input_string .= ":" . $value;
		}
		
		$result = rrd_update($location, $input_string);
		if ($result) {
		    print rrd_error();
		    return false;
		}
		return true;	
	}
	
	function create_image($rrd_file, $image_file, $time='1d', $type='traffic') {
		switch ($type) {
    		case 'traffic':
				$opts = array("--imgformat=PNG", "--start=-".$time, "--end=-300", "--title=Traffic graph",
						"--rigid", "--base=1000", "--height=120", "--width=500",
			        	"--alt-autoscale-max", "--lower-limit=0", "--vertical-label=bits per second", "--slope-mode",
						"DEF:a=".$rrd_file.":traffic_in:AVERAGE",
						"DEF:b=".$rrd_file.":traffic_out:AVERAGE",
						"CDEF:cdefa=a,8,*",
						"CDEF:cdefe=b,8,*",
						"AREA:cdefa#00E600: Inbound",
						"GPRINT:cdefa:LAST:Current\\:%8.2lf %s",
						"GPRINT:cdefa:AVERAGE:Average\\:%8.2lf %s",
						"GPRINT:cdefa:MAX:Maximum\\:%8.2lf %s\\n",
						"LINE1:cdefe#002A97:Outbound",
						"GPRINT:cdefe:LAST:Current\\:%8.2lf %s",
						"GPRINT:cdefe:AVERAGE:Average\\:%8.2lf %s",
						"GPRINT:cdefe:MAX:Maximum\\:%8.2lf %s\\n"
				);
        		break;
		}

		$ret = rrd_graph($image_file, $opts, count($opts));
		if (!is_array($ret)) {
			$error = rrd_error();
			print $error;
			return false;
		}
		else return true;
	}
}