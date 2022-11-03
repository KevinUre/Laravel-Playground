const { trace }  = require("@opentelemetry/api");
const { BasicTracerProvider, ConsoleSpanExporter, SimpleSpanProcessor }  = require("@opentelemetry/sdk-trace-base");
const { JaegerExporter } = require ('@opentelemetry/exporter-jaeger');
const process = require('process');
const opentelemetry = require('@opentelemetry/sdk-node');
const { getNodeAutoInstrumentations } = require('@opentelemetry/auto-instrumentations-node');
const { Resource } = require('@opentelemetry/resources');
const { SemanticResourceAttributes } = require('@opentelemetry/semantic-conventions');
const { HttpInstrumentation } = require('@opentelemetry/instrumentation-http');

const options = {
  tags: [], // optional
  // You can use the default UDPSender
  host: 'localhost', // optional
  port: 6832, // optional
  // OR you can use the HTTPSender as follows
  // endpoint: 'http://localhost:14268/api/traces',
  maxPacketSize: 65000 // optional
}
const exporter = new JaegerExporter(options);

const sdk = new opentelemetry.NodeSDK({
  resource: new Resource({
    [SemanticResourceAttributes.SERVICE_NAME]: 'my-service-testing',
  }),
  exporter,
  // instrumentations: [getNodeAutoInstrumentations()]
  instrumentations: [new HttpInstrumentation()]
});

sdk.start()
  .then(() => console.log('Tracing initialized'))
  .catch((error) => console.log('Error initializing tracing', error));



const http = require('http');
payload = {
  "email": "kevin@trytradeup.com",
  "password": "Qwerty123!"
}

var httpoptions = {
  host: '127.0.0.1',
  path: '/api/apilogin',
  port: '8000',
  method: 'POST',
}
var req = http.request(httpoptions, (response) => {
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

setTimeout(() => { console.log('Completed.'); }, 5000);

sdk.shutdown()

setTimeout(() => { console.log('Done.'); }, 5000);


process.on('SIGTERM', () => {
  sdk.shutdown()
    .then(() => console.log('Tracing terminated'))
    .catch((error) => console.log('Error terminating tracing', error))
    .finally(() => process.exit(0));
});






// const genRanHex = size => [...Array(size)].map(() => Math.floor(Math.random() * 16).toString(16)).join('');
// var tracerConfig = {
//   idGenerator: {
//     generateTraceId: () => genRanHex(16),
//     generateSpanId: () => genRanHex(16),
//   }
// };

// // Create and register an SDK
// const provider = new BasicTracerProvider(tracerConfig);
// // provider.addSpanProcessor(new SimpleSpanProcessor(new ConsoleSpanExporter()));
// provider.addSpanProcessor(new SimpleSpanProcessor(exporter));
// trace.setGlobalTracerProvider(provider);

// // Acquire a tracer from the global tracer provider which will be used to trace the application
// const name = 'my-application-name';
// const version = '0.1.0';
// const tracer = trace.getTracer(name, version);

// // Trace your application by creating spans
// async function operation() {
//   const span = tracer.startSpan("do operation");
//   span.setAttribute("service.name","testing");

//   // mock some work by sleeping 1 second
//   await new Promise((resolve, reject) => {
//     setTimeout(resolve, 1000);
//   })

//   span.end();
// }

// async function main() {
//   while (true) {
//     await operation();
//   }
// }

// main();


