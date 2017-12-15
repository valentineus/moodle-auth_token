#!/bin/sh
# Author:       Valentin Popov
# Email:        info@valentineus.link
# Date:         2017-12-15
# Usage:        /bin/sh build.sh
# Description:  Build the final package for installation in Moodle.

# Updating the Environment
PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
export PATH="$PATH:/usr/local/scripts"

# Build the package
cd ..
mv "./moodle-auth_token" "./auth_token"
zip -9 -r "auth_token.zip" "auth_token"  \
        -x "auth_token/.git*"            \
        -x "auth_token/.travis.yml"      \
        -x "auth_token/.CONTRIBUTING.md" \
        -x "auth_token/build.sh"

# End of work
exit 0