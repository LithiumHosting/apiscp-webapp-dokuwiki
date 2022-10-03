# DokuWiki Web App

This is a 3rd Party module for DokuWiki on ApisCP. 3rd Party modules are *NOT* shipped with ApisCP.

## Getting started

```bash
cd /usr/local/apnscp
mkdir -p config/custom/webapps
git clone https://github.com/lithiumhosting/apiscp-webapp-dokuwiki config/custom/webapps/dokuwiki
./composer dump-autoload -o
cpcmd webapp:refresh-apps
```

**Happy coding!**

## License
TBD
