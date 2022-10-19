const axios = require('axios');

(async () => {
  axios.get("https://jsonplaceholder.typicode.com/posts/1").then(response => {
    console.log(response.data);
  })
})()

