<?php
/* $Revision: 1.4 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-09-19 10:50:29 -0500 (Fri, 19 Sep 2008) $
 * $Rev: 402 $
 */
/* definition of the ReceiptBatch class */

Class Receipt_Batch {

	var $Items; /*array of objects of Receipt class - id is the pointer */
	var $BatchNo; /*Batch Number*/
	var $Account; /*Bank account GL Code banked into */
	var $BankAccountName; /*Bank account name */
	var $DateBanked; /*Date the batch of receipts was banked */
	var $ExRate; /*Exchange rate applicable to all receipts in the batch seperate batch required for different currencies*/
	var $Currency; /*Currency being banked - defaulted to company functional */
	var $Narrative;
	var $ReceiptType;  /*Type of receipt ie credit card/cash/cheque etc - array of types defined in config.php*/
	var $total;	  /*Total of the batch of receipts in the currency of the company*/
	var $ItemCounter; /*Counter for the number of customer receipts in the batch */
	var $Cobrador_id; /* Se Agrega ID de Cobrador para ARMedica*/
/*************************************************************************************************************************/
	var $BankAccountCur;
/*************************************************************************************************************************/
	function Receipt_Batch(){
	/*Constructor function initialises a new receipt batch */
		$this->Items = array();
		$this->ItemCounter=0;
		$this->total=0;
	}

	function add_to_batch($Amount, $Customer, $Discount, $Narrative, $GLCode, $PayeeBankDetail, $CustomerName,$ReceiptTypePartida,$Cobrador_id){
		if ((isset($Customer)||isset($GLCode)) && ($Amount + $Discount) !=0){
			$this->Items[$this->ItemCounter] = new Receipt($Amount, $Customer, $Discount, $Narrative, $this->ItemCounter, $GLCode, $PayeeBankDetail, $CustomerName,$ReceiptTypePartida,$Cobrador_id);
			$this->ItemCounter++;
			$this->total = $this->total + ($Amount + $Discount) / $this->ExRate;
			Return ($this->ItemCounter-1);
		}
		Return 0;
	}

	function remove_receipt_item($RcptID){

		$this->total = $this->total - ($this->Items[$RcptID]->Amount + $this->Items[$RcptID]->Discount) / $this->ExRate;
		unset($this->Items[$RcptID]);

	}

	// bowikaxu Realhost - 13 sept 2008 - add an invoice or transaction to receipt
	function add_invoice($RcptID, $InvNo, $Amt){
		$this->Items[$RcptID]->add_Invoice($InvNo,$Amt);
	}

} /* end of class defintion */

Class Receipt {
	Var $Amount;	/*in currency of the customer*/
	Var $Customer; /*customer code */
	Var $CustomerName;
	Var $Discount;
	Var $Narrative;
	Var $GLCode;
	Var $PayeeBankDetail;
	Var $ID;
	Var $Invoices = array();
	Var $Inv_Amounts = array();
    Var $ReceiptTypePartida;
    Var $Cobrador_id;

	function Receipt ($Amt, $Cust, $Disc, $Narr, $id, $GLCode, $PayeeBankDetail, $CustomerName,$ReceiptTypePartida, $Cobrador_id){

/* Constructor function to add a new Receipt object with passed params */
		$this->Amount =$Amt;
		$this->Customer = $Cust;
		$this->CustomerName = $CustomerName;
		$this->Discount = $Disc;
		$this->Narrative = $Narr;
		$this->GLCode = $GLCode;
		$this->PayeeBankDetail=$PayeeBankDetail;
		$this->ID = $id;
        $this->ReceiptTypePartida=$ReceiptTypePartida;
        $this->Cobrador_id = $Cobrador_id;
	}

	// bowikaxu - Realhost - 13 Sept 2008
	function add_Invoice($InvoiceNo, $Amount){
		//if($Amount<=0)$Amount=0;
		$this->Invoices[$InvoiceNo] = $InvoiceNo;
		$this->Inv_Amounts[$InvoiceNo] = $Amount;
	}

}

?>
