# RoadRunner Slim(w/PHP-DI) Example

## Install & Run

```
composer install
composer run server
```

## Result

```
% curl http://localhost:8080/ 
Hello World

% wrk http://localhost:8080/ 
Running 10s test @ http://localhost:8080/
  2 threads and 10 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     4.69ms    7.23ms  87.37ms   97.06%
    Req/Sec     1.34k   229.45     1.68k    80.00%
  26661 requests in 10.02s, 2.90MB read
Requests/sec:   2660.36
Transfer/sec:    296.17KB
```
