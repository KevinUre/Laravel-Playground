module.exports = {
  apps : [
    {
      name: "laravel-test-app",
      cwd: '/usr/app/',
      script: "node_modules/next/dist/bin/next",
      args: "start",
      exec_mode: 'cluster',
      instances: 5,
      env: {
        NEXT_PUBLIC_BACKEND_URL: "http://localhost:8000"
      }
    }
  ]
}