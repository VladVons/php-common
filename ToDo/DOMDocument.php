<?php
        private function GetCategoryTreeRecurs($aParentID, $aLevel, $aXML, $aNode)
	{
	    $ArrItems = $this->GetCategories($aParentID);
    	    $ArrItems->Reset();
            while (list($Label, $Value) = $ArrItems->Each()) {
		//printf("\nDbg: Level: $aLevel, PID: $aParentID, ID: $Label, Name: $Value\n");   
		$Elem = $aXML->createElement("Category");

		$Attr = $aXML->createAttribute('Value');
		$Attr->value = 0;
		$Elem->appendChild($Attr);	

		$Attr = $aXML->createAttribute('Id');
		$Attr->value = $Label;
		$Elem->appendChild($Attr);	

		$Attr = $aXML->createAttribute('Name');
		$Attr->value = $Value;
		$Elem->appendChild($Attr);	

		$aNode->appendChild($Elem);

		$this->GetCategoryTreeRecurs($Label, $aLevel + 1, $aXML, $Elem);

		//$ArrProd = $this->GetProducts($Label);
		//$ArrProd->Show();          
	    }
        }

	
	public function GetCategoryTree()
	{
	    $doc = new DOMDocument("1.0", "UTF-8");
	    $doc->formatOutput = true;

	    $Elem = $doc->createElement("root");
	    $Node = $doc->appendChild($Elem);
	    $this->GetCategoryTreeRecurs(0, 0, $doc, $Node);
	    $doc->saveXML();

	    return $doc;
	}
?>