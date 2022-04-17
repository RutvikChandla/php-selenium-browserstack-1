<?php
    require_once("vendor/autoload.php");
    use Facebook\WebDriver\Remote\RemoteWebDriver;
    use Facebook\WebDriver\WebDriverBy;
    use Facebook\WebDriver\WebDriverExpectedCondition;
    use BrowserStack\Local;
    
    $BROWSERSTACK_USERNAME = "BROWSERSTACK_USERNAME";
    $BROWSERSTACK_ACCESS_KEY = "BROWSERSTACK_ACCESS_KEY";

    # Creates an instance of Local
    $bs_local = new Local();

    # You can also set an environment variable - "BROWSERSTACK_ACCESS_KEY".
    $bs_local_args = array("key" => $BROWSERSTACK_ACCESS_KEY);
    # Starts the Local instance with the required arguments
    $bs_local->start($bs_local_args);

    # Check if BrowserStack local instance is running
    echo $bs_local->isRunning();
    $caps = array(
        "browserName" => "iPhone",
        "device" => "iPhone 11",
        "realMobile" => "true",
        "os_version" => "14.0",
        "browserstack.local" => "true",
        "name" => "BStack-[Php] Sample Test", // test name
        "build" => "BStack Build Number 1" // CI/CD job or build name
    );

    $web_driver = RemoteWebDriver::create("https://$BROWSERSTACK_USERNAME:$BROWSERSTACK_ACCESS_KEY@hub-cloud.browserstack.com/wd/hub",$caps);
    try{
        $web_driver->get("http://bs-local.com:45691/check");
        $body_text = $web_driver->wait(10000)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("body")))->getText();
        # Setting the status of test as 'passed' or 'failed' based on the condition; if title of the web page starts with 'BrowserStack'
        if ($body_text == "Up and running"){
            $web_driver->executeScript('browserstack_executor: {"action": "setSessionStatus", "arguments": {"status":"passed", "reason": "Local test ran successfully"}}' );
        }  else {
            $web_driver->executeScript('browserstack_executor: {"action": "setSessionStatus", "arguments": {"status":"failed", "reason": "Failed to load local test"}}');
        }
    }
    catch(Exception $e){
        echo 'Message: ' .$e->getMessage();
    }
    $web_driver->quit();
      # Stop the Local instance
    $bs_local->stop();
?>
