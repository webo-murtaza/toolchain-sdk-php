```
$ve_chain_sdk = new \VeChainSDK\VeChainSDK($app_id, $app_key, $operator_uid);
```

```
$request_number = time() . rand(111111111, 999999999);
$vid_response  = $ve_chain_sdk->generateVID($request_number);
```

```
$data_hash = hash('sha256', 'murtaza@alchemytech.ca');
$vid = '';
$request_number = time() . rand(111111111, 999999999);
$hash_response  = $ve_chain_sdk->createHash($data_hash, $vid, $request_number);
```

## Notes

- Call functions asynchronously to get the success response from the VeChain ToolChain
- Request number is the only way to get the updated statuses
- Statuses are... draft, init, processing, success