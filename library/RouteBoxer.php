<?php
class RouteBoxer
{
	private $_grid;
	private $_latGrid = array();
	private $_lngGrid = array();
	private $_boxesX = array();
	private $_boxesY = array();
	
	public function box($vertices, $range)
	{
		$this->_buildGrid($vertices, $range);
		$this->_findIntersectingCells($vertices);
		$this->_mergeIntersectingCells();
		
		return
			count($this->_boxesX) <= count($this->_boxesY) ?
			$this->_boxesX :
			$this->_boxesY ;
	}
	
	function _buildGrid($vertices, $range)
	{
		$routeBounds = new LatLngBounds();
		foreach ($vertices as $vertex)
			$routeBounds->extend($vertex);
		
		$routeBoundsCenter = $routeBounds->getCenter();
		
		array_push($this->_latGrid, $routeBoundsCenter->lat());
		
		array_push($this->_latGrid, $routeBoundsCenter->rhumbDestinationPoint(0, $range)->lat());
		for ($i = 2; $this->_latGrid[$i - 2] < $routeBounds->getNorthEast()->lat(); $i++)
			array_push($this->_latGrid, $routeBoundsCenter->rhumbDestinationPoint(0, $range * $i)->lat());
		
		for ($i = 1; $this->_latGrid[1] > $routeBounds->getSouthWest()->lat(); $i++)
			array_unshift($this->_latGrid, $routeBoundsCenter->rhumbDestinationPoint(180, $range * $i)->lat());
		
		array_push($this->_lngGrid, $routeBoundsCenter->lng());
		
		array_push($this->_lngGrid, $routeBoundsCenter->rhumbDestinationPoint(90, $range)->lng());
		for ($i = 2; $this->_lngGrid[$i - 2] < $routeBounds->getNorthEast()->lng(); $i++)
			array_push($this->_lngGrid, $routeBoundsCenter->rhumbDestinationPoint(90, $range * $i)->lng());
		
		for ($i = 1; $this->_lngGrid[1] > $routeBounds->getSouthWest()->lng(); $i++)
			array_unshift($this->_lngGrid, $routeBoundsCenter->rhumbDestinationPoint(270, $range * $i)->lng());
	}
	
	function _findIntersectingCells($vertices)
	{
		$hintXY = $this->_getCellCoords($vertices[0]);
		
		$this->_markCell($hintXY);
		
		for ($i = 1; $i < count($vertices); $i++)
		{
			$gridXY = $this->_getGridCoordsFromHint($vertices[$i], $vertices[$i - 1], $hintXY);
			
			if ($gridXY[0] == $hintXY[0] && $gridXY[1] == $hintXY[1])
				continue;
			else if ((abs($hintXY[0] - $gridXY[0]) == 1 && $hintXY[1] == $gridXY[1]) ||
				($hintXY[0] == $gridXY[0] && abs($hintXY[1] - $gridXY[1]) == 1))
				$this->_markCell($gridXY);
			else
				$this->_getGridIntersects($vertices[$i - 1], $vertices[$i], $hintXY, $gridXY);
			
			$hintXY = $gridXY;
		}
	}
	
	function _getCellCoords($latlng)
	{
		for ($x = 0; $this->_lngGrid[$x] < $latlng->lng(); $x++) {}
		for ($y = 0; $this->_latGrid[$y] < $latlng->lat(); $y++) {}
		
		return array($x - 1, $y - 1);
	}
	
	function _getGridCoordsFromHint($latlng, $hintlatlng, $hint)
	{
		if ($latlng->lng() > $hintlatlng->lng())
			for ($x = $hint[0]; $this->_lngGrid[$x + 1] < $latlng->lng(); $x++) {}
		else
			for ($x = $hint[0]; $this->_lngGrid[$x] > $latlng->lng(); $x--) {}
		if ($latlng->lat() > $hintlatlng->lat())
			for ($y = $hint[1]; $this->_latGrid[$y + 1] < $latlng->lat(); $y++) {}
		else
			for ($y = $hint[1]; $this->_latGrid[$y] > $latlng->lat(); $y--) {}
		
		return array($x, $y);
	}
	
	function _getGridIntersects($start, $end, $startXY, $endXY)
	{
		$bearing = $start->rhumbBearingTo($end);
		
		$hint = $start;
		$hintXY = $startXY;
		
		if ($end->lat() > $start->lat())
		{
			for ($i = $startXY[1] + 1; $i <= $endXY[1]; $i++)
			{
				$edgePoint = $this->_getGridIntersect($start, $bearing, $this->_latGrid[$i]);
				
				$edgeXY = $this->_getGridCoordsFromHint($edgePoint, $hint, $hintXY);
				
				$this->_fillInGridSquares($hintXY[0], $edgeXY[0], $i - 1);
				
				$hint = $edgePoint;
				$hintXY = $edgeXY;
			}
			
			$this->_fillInGridSquares($hintXY[0], $endXY[0], $i - 1);
		}
		else
		{
			for ($i = $startXY[1]; $i > $endXY[1]; $i--)
			{
				$edgePoint = $this->_getGridIntersect($start, $bearing, $this->_latGrid[$i]);
				
				$edgeXY = $this->_getGridCoordsFromHint($edgePoint, $hint, $hintXY);
				
				$this->_fillInGridSquares($hintXY[0], $edgeXY[0], $i);
				
				$hint = $edgePoint;
				$hintXY = $edgeXY;
			}
			
			$this->_fillInGridSquares($hintXY[0], $endXY[0], $i);
		}
	}
	
	function _getGridIntersect($start, $bearing, $gridLineLat)
	{
		$d = 6371 * ((deg2rad($gridLineLat) - deg2rad($start->lat())) / cos(deg2rad($bearing)));
		return $start->rhumbDestinationPoint($bearing, $d);
	}
	
	function _fillInGridSquares($startx, $endx, $y)
	{
		if ($startx < $endx)
			for ($x = $startx; $x <= $endx; $x++)
				$this->_markCell(array($x, $y));
		else
			for ($x = $startx; $x >= $endx; $x--)
				$this->_markCell(array($x, $y));
	}
	
	function _markCell($cell)
	{
		$x = $cell[0];
		$y = $cell[1];
		$this->_grid[$x - 1][$y - 1] = 1;
		$this->_grid[$x][$y - 1] = 1;
		$this->_grid[$x + 1][$y - 1] = 1;
		$this->_grid[$x - 1][$y] = 1;
		$this->_grid[$x][$y] = 1;
		$this->_grid[$x + 1][$y] = 1;
		$this->_grid[$x - 1][$y + 1] = 1;
		$this->_grid[$x][$y + 1] = 1;
		$this->_grid[$x + 1][$y + 1] = 1;
	}
	
	function _mergeIntersectingCells()
	{
		$currentBox = null;
		
		for ($y = 0; $y < count($this->_latGrid); $y++)
		{
			for ($x = 0; $x < count($this->_lngGrid); $x++)
			{
				if (isset($this->_grid[$x]) && isset($this->_grid[$x][$y]))
				{
					$box = $this->_getCellBounds(array($x, $y));
					if (null != $currentBox)
						$currentBox->extend($box->getNorthEast());
					else
						$currentBox = $box;
				}
				else
				{
					$this->_mergeBoxesY($currentBox);
					$currentBox = null;
				}
			}
			
			$this->_mergeBoxesY($currentBox);
			$currentBox = null;
		}
		
		for ($x = 0; $x < count($this->_lngGrid); $x++)
		{
			for ($y = 0; $y < count($this->_latGrid); $y++)
			{
				if (isset($this->_grid[$x]) && isset($this->_grid[$x][$y]))
				{
					$box = $this->_getCellBounds(array($x, $y));
					if (null != $currentBox)
						$currentBox->extend($box->getNorthEast());
					else
						$currentBox = $box;
				}
				else
				{
					$this->_mergeBoxesX($currentBox);
					$currentBox = null;
				}
			}
			
			$this->_mergeBoxesX($currentBox);
			$currentBox = null;
		}
	}
	
	function _mergeBoxesX($box)
	{
		if (null != $box)
		{
			for ($i = 0; $i < count($this->_boxesX); $i++)
			{
				if ($this->_boxesX[$i]->getNorthEast()->lng() == $box->getSouthWest()->lng() &&
					$this->_boxesX[$i]->getSouthWest()->lat() == $box->getSouthWest()->lat() &&
					$this->_boxesX[$i]->getNorthEast()->lat() == $box->getNorthEast()->lat())
				{
					$this->_boxesX[$i]->extend($box->getNorthEast());
					return;
				}
			}
			array_push($this->_boxesX, $box);
		}
	}
	
	function _mergeBoxesY($box)
	{
		if (null != $box)
		{
			for ($i = 0; $i < count($this->_boxesY); $i++)
			{
				if ($this->_boxesY[$i]->getNorthEast()->lat() == $box->getSouthWest()->lat() &&
					$this->_boxesY[$i]->getSouthWest()->lng() == $box->getSouthWest()->lng() &&
					$this->_boxesY[$i]->getNorthEast()->lng() == $box->getNorthEast()->lng())
				{
					$this->_boxesY[$i]->extend($box->getNorthEast());
					return;
				}
			}
			array_push($this->_boxesY, $box);
		}
	}
	
	function _getCellBounds($cell)
	{
		return
			new LatLngBounds(
				new LatLng($this->_latGrid[$cell[1]], $this->_lngGrid[$cell[0]]),
				new LatLng($this->_latGrid[$cell[1] + 1], $this->_lngGrid[$cell[0] + 1])
			);
	}
	
}
class LatLng
{
	private $_lat;
	private $_lng;
	
	function __construct($lat, $lng)
	{
		$this->_lat = $lat;
		$this->_lng = $lng;
	}
	
	public function lat()
	{
		return $this->_lat;
	}
	
	public function lng()
	{
		return $this->_lng;
	}
	
	public function rhumbDestinationPoint($bearing, $distance)
	{
		$R = 6371;
		$d = $distance / $R;
		$lat = deg2rad($this->lat());
		$lng = deg2rad($this->lng());
		$bearing = deg2rad($bearing);
		
		$lat2 = $lat + $d * cos($bearing);
		$dLat = $lat2 - $lat;
		$dPhi = log(tan($lat2 / 2 + pi() / 4) / tan($lat / 2 + pi() / 4));
		$q = (abs($dLat) > 0.0000000001) ? $dLat / $dPhi : cos($lat);
		$dLng = $d * sin($bearing) / $q;
		if (abs($lat2) > pi() / 2)
			$lat2 = $lat2 > 0 ? pi() - $lat2 : -(pi() - $lat2);
		$lng2 = fmod(($lng + $dLng + pi()), (2 * pi())) - pi();
		
		if (is_nan($lat2) or is_nan($lng2))
			return null;
		
		return
			new LatLng(rad2deg($lat2), rad2deg($lng2));
	}
	
	public function rhumbBearingTo($dest)
	{
		$dLng = deg2rad($dest->lng() - $this->lng());
		$dPhi = log(tan(deg2rad($dest->lat()) / 2 + pi() / 4) / tan(deg2rad($this->lat()) / 2 + pi() / 4));
		if (abs($dLng) > pi())
			$dLng = $dLng > 0 ? -(2 * pi() - $dLng) : (2 * pi() + $dLng);
		
		return
			$this->toBearing(atan2($dLng, $dPhi));
	}
	
	public function toBearing($bearing)
	{
		return fmod(rad2deg($bearing) + 360, 360);
	}
	
}
class LatLngBounds
{
	private $_NE;
	private $_SW;
	
	function __construct($sw = null, $ne = null)
	{
		if (null == $ne) $ne = new LatLng(-180, -180);
		if (null == $sw) $sw = new LatLng(180, 180);
		
		$this->_NE = $ne;
		$this->_SW = $sw;
	}
	
	public function getNorthEast()
	{
		return $this->_NE;
	}
	
	public function getSouthWest()
	{
		return $this->_SW;
	}
	
	public function extend($latlng)
	{
		$neLat = $this->_NE->lat();
		$neLng = $this->_NE->lng();
		$swLat = $this->_SW->lat();
		$swLng = $this->_SW->lng();
		
		if ($latlng->lat() > $neLat) $neLat = $latlng->lat();
		if ($latlng->lng() > $neLng) $neLng = $latlng->lng();
		if ($latlng->lat() < $swLat) $swLat = $latlng->lat();
		if ($latlng->lng() < $swLng) $swLng = $latlng->lng();
		
		$this->_NE = new LatLng($neLat, $neLng);
		$this->_SW = new LatLng($swLat, $swLng);
	}
	
	public function getCenter()
	{
		$lat = $this->_SW->lat() + ($this->_NE->lat() - $this->_SW->lat()) / 2;
		$lng = $this->_SW->lng() + ($this->_NE->lng() - $this->_SW->lng()) / 2;
		
		return
			new LatLng($lat, $lng);
	}
}
?>