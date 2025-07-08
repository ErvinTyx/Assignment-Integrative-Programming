# Installation for a localhost email verification sample

["Go to mailpit for real installation guide"]("https://mailpit.axllent.org/docs/install/")


run mailpit in cmd
```cmd
mailpit
```

you can open the sample email site at
`localhost:8025` or `127.0.0.1:8025`

then go to env file and change the mention variable
```.env
# default is log it will be store in storage log
MAIL_MAILER=smtp
# the receiving port
MAIL_PORT=1025
```