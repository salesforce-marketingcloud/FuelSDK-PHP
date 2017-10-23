# 1.1.0 (2017-10-19)
* namespace integration in all source, test and sample code
* composer autoload issue fix
* newly supported objects:
    - Result Message
    - Data Extract
    - Triggered Send Summary

# 1.0.0 (2017-07-18)

### New Features 

* **mcrypt :** mcrypt dependency removed.
* **proxy :** added proxy server support.
* **jwt :** jwt.php is removed from project source structure and added as dependency.
* **soap-wsse :** soap-wsse.php is removed from project source structure and added as dependency.
* **code refactor :** code refactored to individual class files. (under src/ directory)
* **unit test :** added unit test cases (happy path for now) using phpunit testing framework. (under tests/ directory)
* **API docs :** added API documentation using phpdocumentor framework. (under docs/ directory)
* **auto loader :** integrated auto loader (spl_autoload_register) for all source code under src/, tests/, objsamples/ directory.
