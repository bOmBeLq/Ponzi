Ponzi script
========================
Sorry for poor documentation but in the beginning I didn't expect to publish this script.

I take no responsibility for any money loss caused by this script.

If You want to run this script but it does not work You can submit an issue ticket.
I may help u but remember that You need some technical knowledge to run this.

If You liked it and want to donate some satoshi:

BTC address: 19VMMFXX2vTiAZZabc6GfENBw7zLjJzG1Y

1) Installation
----------------------------------
- setup mysql DB
- setup bitcoind (or other daemon) client
- get a composer
- run composer install and follow instructions
- run app/console doctrine:database:create
- run app/console doctrine:schema:create
- setup cron for command app/console bml:wallet:scan (5 minutes should be OK) this command will keep updating transactions, stats and sending payouts

2) Payour percent, fee etc. settings
----------------------------------
To setup round settings go to /admin url.

Admin access is configured in parameters.yml:

- admin_user: admin
- admin_password: test
- admin_ip: 127.0.0.1

Set admin_ip to Your IP address. You can leave admin_ip blank (use ~) but it will be less safe because admin panel uses simple http authentication.
