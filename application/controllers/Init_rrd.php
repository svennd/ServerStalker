<?php
defined('BASEPATH') OR exit('No direct script access allowed');

# document is ran only once
class Init_rrd extends CI_Controller {

	# fields
		# DS : datasource
		# $name
		# method? : 
			# GAUGE (temperature, ... )
			# COUNTER (always grows)
			# DCOUNTER | DERIVE | DDERIVE
			# ABSOLUTE (reset on read, ex: last # of messages since update)
		# heartbeat : number of seconds before a unknown is assumed
		# min : 
		# max : U : unknown
		# example : DS:traffic_in:DERIVE:600:0:U
		
		# RRA : round robin archives
		# consolidation function
		# AVERAGE | MIN | MAX | LAST
		# xff : 0.5 integral over unknowns
		# steps to store : 	1 would mean save $value every --step (5min default)
		#					2 would mean save $value every 2*--steps (10min),
		#					and take the method of the 2 values
		# rows to be stored in total 
	
	# default values
	public $steps = 600; # every 10 minutes is more then enough
	public $heartbeat = 600*2;
	public $setup = array("--step", $this->step, "--start", time());
	
	# default rra
	public $rra_avg = array( # save a value every x, total history y
							"RRA:AVERAGE:0.5:1:1008", # 10 min(x) / 6*24*7 = 7 days
							"RRA:AVERAGE:0.5:6:720",  # 1 hour / 24*30 = 30 days
							"RRA:AVERAGE:0.5:72:360", # 12 hour / 2*30*6 = 6 months
							"RRA:AVERAGE:0.5:168:797", # 
							"RRA:AVERAGE:0.5:288:797", # 
					);

	public function index()
	{
		# load library
		$this->load->library('rrd');
		
		# create traffic
		#$this->rrd->create($this->traffic);
	}
	
	# no need for 1 min load when we only poll
	# every 10 minutes (even 5 is overkill)
	private function cpu()
	{
		# load is roughly core count = heavy load, more then core count = problems
		return array(	
						# defaults are oke
						$this->setup,
		
						array(
							# datasources
							"DS:cpu_load_five:GAUGE:" . $this->heartbeat . ":0:U",
							"DS:cpu_load_fifeteen:GAUGE:" . $this->heartbeat . ":0:U",
						),
						# 
						
						"RRA:MIN:0.5:1:600",
						"RRA:MIN:0.5:6:700", 
						"RRA:MIN:0.5:24:775",
						"RRA:MIN:0.5:288:797",
						
						"RRA:MAX:0.5:1:600",
						"RRA:MAX:0.5:6:700",
						"RRA:MAX:0.5:24:775",
						"RRA:MAX:0.5:288:797", 
						
						"RRA:LAST:0.5:1:600",
						"RRA:LAST:0.5:6:700",
						"RRA:LAST:0.5:24:775",
						"RRA:LAST:0.5:288:797"
					)
				);	
	}
	
	private function traffic()
	{
		$opts = array(
						"--step", $step,
						"--start", time(), 
						"DS:traffic_in:DERIVE:600:0:U",
						"DS:traffic_out:DERIVE:600:0:U",
						"RRA:AVERAGE:0.5:1:600", 
						"RRA:AVERAGE:0.5:6:700",
						"RRA:AVERAGE:0.5:24:775",
						"RRA:AVERAGE:0.5:288:797",
						"RRA:MIN:0.5:1:600",
						"RRA:MIN:0.5:6:700", 
						"RRA:MIN:0.5:24:775",
						"RRA:MIN:0.5:288:797",
						"RRA:MAX:0.5:1:600",
						"RRA:MAX:0.5:6:700",
						"RRA:MAX:0.5:24:775",
						"RRA:MAX:0.5:288:797", 
						"RRA:LAST:0.5:1:600",
						"RRA:LAST:0.5:6:700",
						"RRA:LAST:0.5:24:775",
						"RRA:LAST:0.5:288:797"
					);
	}
}
