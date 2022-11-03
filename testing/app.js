const axios = require('axios');
const http = require('http');

// (async () => {
//   payload = {
//     "email": "kevin@trytradeup.com",
//     "password": "Qwerty123!"
//   }
//   const response = await axios.post("http://127.0.0.1:8000/api/apilogin", payload)
//   console.log(response.data)
//   // axios.get("https://jsonplaceholder.typicode.com/posts/1").then(response => {
//   //   console.log(response.data);
//   // })
// })()

payload = {
  "email": "kevin@trytradeup.com",
  "password": "Qwerty123!"
}

var options = {
  host: '127.0.0.1',
  path: '/api/apilogin',
  port: '8000',
  method: 'POST',
}
var req = http.request(options, (response) => {
  var str = '';
  response.on('data', function (chunk) {
    str += chunk;
  });
  response.on('end', function () {
    console.log(str);
  });
});

req.write(JSON.stringify(payload));
req.end();