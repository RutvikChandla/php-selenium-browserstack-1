<?php
require_once('vendor/autoload.php');
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
$caps = array(
    "browserName" => "iPhone",
    "device" => "iPhone 11",
    "realMobile" => "true",
    "os_version" => "14.0",
    "name" => "BStack-[Php] Sample Test", // test name
    "build" => "BStack Build Number 1" // CI/CD job or build name
);

$BROWSERSTACK_USERNAME = "BROWSERSTACK_USERNAME";
$BROWSERSTACK_ACCESS_KEY = "BROWSERSTACK_ACCESS_KEY";

$web_driver = RemoteWebDriver::create("https://$BROWSERSTACK_USERNAME:$BROWSERSTACK_ACCESS_KEY@hub-cloud.browserstack.com/wd/hub",$caps);
try{
    $web_driver->get("https://bstackdemo.com/");
    $web_driver->wait(10000)->until(WebDriverExpectedCondition::titleIs("StackDemo"));
    # getting text of the product
    $product_on_page = $web_driver->wait(10000)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::XPath("//*[@id='1']/p")))->getText();
    # clicking the 'Add to cart' button
    $web_driver->wait(10000)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::XPath("//*[@id='1']/div[4]")))->click();
    # checking whether the cart pane is present on webpage
    $web_driver->wait(10000)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className("float-cart__content")));
    # getting text of the product
    $product_in_cart = $web_driver->wait(10000)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::XPath("//*[@id='__next']/div/div/div[2]/div[2]/div[2]/div/div[3]/p[1]")))->getText();
    # Setting the status of test as 'passed' or 'failed' based on the condition; if title of the web page starts with 'BrowserStack'
    if ($product_on_page == $product_in_cart){
        $web_driver->executeScript('browserstack_executor: {"action": "setSessionStatus", "arguments": {"status":"passed", "reason": "Product has been successfully added to the cart!"}}' );
    }  else {
        $web_driver->executeScript('browserstack_executor: {"action": "setSessionStatus", "arguments": {"status":"failed", "reason": "Failed to add product to the cart or some elements might have failed to load."}}');
    }
}
catch(Exception $e){
    echo 'Message: ' .$e->getMessage();
}
$web_driver->quit();
?>
