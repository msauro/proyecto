<?php
	
class Gabinando_CancelOldRequests extends Application_Model_Base
{
	protected $_name = 'requests';

	// Set requests status to 'Cancelled' for those that were made on the current day 
	// but are still on 'Pending' status
	public function cancelOldRequests(){

		// Get current date
		$currentDate = date('Y-m-d');
		$currentTime = strtotime(date('H:i:s'));

		$query = $this->select()
            ->from($this, array('id_request', 'status', 'date', 'time'))
            ->where('date = ?', $currentDate);
			
        $rows = $this->fetchAll($query);
		$rows = $rows->toArray();

		if(count($rows) > 0){

			for ($i=0; $i < count($rows); $i++) { 
				
				// For requests on 'Pending' status
				if($rows[$i]['status'] == 'Pending'){

					$requestTime = strtotime($rows[$i]['time']);

					// Get date difference in hours
					$interval = ($currentTime - $requestTime) / 3600;

					// If the request is older than 4 hours 
		    		// then set its status to 'Cancelled' and edit it
		    		// on DB
		    		if($interval >= 4){
		    			$rows[$i]['status'] = 'Cancelled'; 
		    			
		    			$this->edit('id_request', $rows[$i]['id_request'], $rows[$i]);
		    		}
				}
			}
		}
	}
}

?>