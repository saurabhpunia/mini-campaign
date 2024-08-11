# mini-campaign
A mini campaign is a place where you can send mails by uploading the csv file

# clone this git repository either by SSH or HTTP
git clone https://github.com/saurabhpunia/mini-campaign.git
# or
git clone git@github.com:saurabhpunia/mini-campaign.git

# create .env file using .env.example file
cp .env.example .env

# modify config in .env file as per requirement
composer install

# migrate the tables
php artisan migrate

# optimize the project
php artisan optimize

# serve the project - for development
php artisan serve

# start the instance of worker
php artisan queue:work