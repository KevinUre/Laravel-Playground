const { trace }  = require("@opentelemetry/api");
const { BasicTracerProvider, ConsoleSpanExporter, SimpleSpanProcessor }  = require("@opentelemetry/sdk-trace-base");
const { JaegerExporter } = require ('@opentelemetry/exporter-jaeger');
const axios = require('axios');

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

// Create and register an SDK
const provider = new BasicTracerProvider();
// provider.addSpanProcessor(new SimpleSpanProcessor(new ConsoleSpanExporter()));
provider.addSpanProcessor(new SimpleSpanProcessor(exporter));
trace.setGlobalTracerProvider(provider);

// Acquire a tracer from the global tracer provider which will be used to trace the application
const name = 'my-application-name';
const version = '0.1.0';
const tracer = trace.getTracer(name, version);

// Trace your application by creating spans
async function operation() {
  const span = tracer.startSpan("do operation");
  span.setAttribute("service.name","frontend");
  // mock some work by sleeping 1 second
  // await new Promise((resolve, reject) => {
  //   setTimeout(resolve, 1000);
  // })
  config = {
    headers: {
      "Uber-Trace-Id": `${span.spanContext().traceId}:${span.spanContext().spanId}:0:1`
    }
  }
  payload = {
    "email": "kevin@trytradeup.com",
    "password": "Qwerty123!"
  }
  const response = await axios.post("http://127.0.0.1:8000/api/apilogin", payload, config)
  console.log(response.data)
  console.log(span.spanContext().traceId);
  span.end();
}

async function main() {
  // while (true) {
    await operation();
  // }
}

main();


