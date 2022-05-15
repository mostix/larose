<?php
  include_once 'config.php';
  include_once 'languages/languages.php';
  include_once 'functions/include-functions.php';
  
  $ENCODED  = $_POST['encoded'];
  $CHECKSUM = $_POST['checksum'];
  
  $easypay_notice_post = "encoded=$ENCODED&checksum=$CHECKSUM";

  # XXX Secret word with which merchant make CHECKSUM on the ENCODED packet
  $secret     = 'MJFFXCAXU9PUKFXT742QDU09F1WHX6V2S0XPIRCBMQM0L737HDUF9YUXGMYP57EF';
  $CHECKSUM_CALC = hmac('sha1', $ENCODED, $secret);

  if($CHECKSUM_CALC == $CHECKSUM) {

    $data = base64_decode($ENCODED);
    $lines_arr = explode("\n", $data);

    foreach ($lines_arr as $line) {

      //echo "$line<br>";

      if(preg_match("/^INVOICE=(\d+):STATUS=(PAID|DENIED|EXPIRED)(:PAY_TIME=(\d+):STAN=(\d+):BCODE=([0-9a-zA-Z]+))?$/", $line, $regs)) {

        $order_id  = $regs[1];
        $status   = $regs[2];

        if(isset($regs[4]) && isset($regs[5]) && isset($regs[6])) {
          $pay_date = $regs[4]; # XXX if PAID
          $stan     = $regs[5]; # XXX if PAID
          $bcode    = $regs[6]; # XXX if PAID
        }

        switch ($status) {

          case "PAID":
              $notice_response = "INVOICE=$order_id:STATUS=OK\n";
            break;
          case "EXPIRED":
              $notice_response = "INVOICE=$order_id:STATUS=OK\n";
            break;
          case "DENIED":
              $notice_response = "INVOICE=$order_id:STATUS=OK\n";
            break;

          default:
            break;
        }
      }
    } //foreach ($lines_arr as $line)

    $query = "UPDATE `order_payment` SET `payment_result`='$status' WHERE `order_id` = '$order_id'";
    mysqli_query($db_link,$query);
  }
  else {
    $notice_response = "ERR=Not valid CHECKSUM";
  }

  echo $notice_response;
    
//  $query = "INSERT INTO `easypay_notices`(`notice_id`, `order_id`, `notice`, `notice_ipv4`, `notice_response`, `notice_date`) 
//                                    VALUES ('','$order_id','$easypay_notice','".$_SERVER['REMOTE_ADDR']."','$notice_response',NOW())";
//  @mysqli_query($db_link,$query);
  $query = "INSERT INTO `easypay_notices`(`notice_id`, `order_id`, `notice`, `notice_ipv4`, `notice_response`, `notice_date`) 
                                    VALUES ('','$order_id','$easypay_notice_post','".$_SERVER['REMOTE_ADDR']."','$notice_response',NOW())";
  @mysqli_query($db_link,$query);
?>
