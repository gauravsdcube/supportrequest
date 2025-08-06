#!/bin/bash

# RequestSupport Module Installation Script for HumHub
# This script installs the RequestSupport module into a HumHub installation

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}RequestSupport Module Installation Script${NC}"
echo "=============================================="

# Check if we're in the right directory
if [ ! -f "Module.php" ]; then
    echo -e "${RED}Error: Please run this script from the RequestSupport module directory${NC}"
    exit 1
fi

# Get HumHub path
read -p "Enter the path to your HumHub installation: " HUMHUB_PATH

if [ ! -d "$HUMHUB_PATH" ]; then
    echo -e "${RED}Error: HumHub installation not found at $HUMHUB_PATH${NC}"
    exit 1
fi

if [ ! -d "$HUMHUB_PATH/protected" ]; then
    echo -e "${RED}Error: Invalid HumHub installation. 'protected' directory not found${NC}"
    exit 1
fi

echo -e "${GREEN}Found HumHub installation at: $HUMHUB_PATH${NC}"

# Create module directory
MODULE_PATH="$HUMHUB_PATH/protected/modules/requestSupport"
echo -e "${YELLOW}Creating module directory...${NC}"
mkdir -p "$MODULE_PATH"

# Copy files
echo -e "${YELLOW}Copying module files...${NC}"
cp -r * "$MODULE_PATH/"

# Set permissions
echo -e "${YELLOW}Setting file permissions...${NC}"
chmod -R 755 "$MODULE_PATH"
find "$MODULE_PATH" -name "*.php" -exec chmod 644 {} \;

# Get web server user
WEB_USER=$(ps aux | grep -E "(apache|nginx|www-data)" | head -1 | awk '{print $1}')
if [ -z "$WEB_USER" ]; then
    WEB_USER="www-data"
fi

echo -e "${YELLOW}Setting ownership to $WEB_USER...${NC}"
chown -R "$WEB_USER:$WEB_USER" "$MODULE_PATH"

# Clear cache
echo -e "${YELLOW}Clearing HumHub cache...${NC}"
rm -rf "$HUMHUB_PATH/protected/runtime/cache/"* 2>/dev/null || true
rm -rf "$HUMHUB_PATH/protected/runtime/HTML/"* 2>/dev/null || true

echo -e "${GREEN}Installation completed successfully!${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Log in to your HumHub admin panel"
echo "2. Go to Administration > Modules"
echo "3. Find 'Request Support' and click Enable"
echo "4. Go to any space and enable the module in Space Settings > Modules"
echo ""
echo -e "${GREEN}For detailed instructions, see:${NC}"
echo "- README.md - General information and usage"
echo "- DEPLOYMENT.md - Detailed deployment guide"
echo "- ENABLE_MODULE.md - Quick enable guide"
echo ""
echo -e "${GREEN}Module files installed at: $MODULE_PATH${NC}" 