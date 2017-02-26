#!/bin/bash

# Install WordPress.
install ()
{

    # Pop the current folder, we'll cd into it before leaving the function.
    local cwd=$(pwd)

    # Get the instance id.
    local instance=$1

    # Create a directory for our WordPres instance and move into it.
    local instance_dir="$cwd/htdocs/$instance"
    mkdir -p $instance_dir
    cd $instance_dir

    # Add --version=<version> to specify which version to download.
    # We should test at least with 4.5, 4.6, 4.7 and nightly test.
    # See https://wp-cli.org/commands/core/download/
    wp core download
    wp core config --dbname=wordpress --dbuser=root --dbprefix="wp_${instance}_"
    wp core install --url="http://wordpress.local/${instance}" --title=WordPress --admin_user=admin --admin_password=admin --admin_email=admin@example.org

    # Create a post to test analysis results.
    wp post create --post_type=post --post_title='A sample post' --post_content='WordLift brings the power of Artificial Intelligence to help you produce richer content and organize it around your audience.'

    # Finally link the WordLift plugin in WordPress.
    ln -s "$HOME/src" "$instance_dir/wp-content/plugins/wordlift"

    cd $cwd

}

i=1
INSTANCES=7

# Get the current folder.
HOME=$(pwd)

while [ $i -lt $INSTANCES ]
do
    install $i
    let "i+=1"
done
