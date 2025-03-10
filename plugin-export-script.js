/**
 * This script helps export the medication administration plugin
 * to be used in another application.
 * 
 * Usage: node plugin-export-script.js /path/to/target/application
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Get target directory from command line arguments
const targetDir = process.argv[2];

if (!targetDir) {
  console.error('Please provide a target directory path');
  console.log('Usage: node plugin-export-script.js /path/to/target/application');
  process.exit(1);
}

// Source and destination paths
const sourcePluginDir = path.join(__dirname, 'src', 'plugins', 'medication-admin');
const targetPluginDir = path.join(targetDir, 'src', 'plugins', 'medication-admin');

// Create target directory if it doesn't exist
if (!fs.existsSync(targetPluginDir)) {
  fs.mkdirSync(targetPluginDir, { recursive: true });
  console.log(`Created directory: ${targetPluginDir}`);
}

// Copy plugin files
function copyDirRecursive(source, target) {
  // Create target directory if it doesn't exist
  if (!fs.existsSync(target)) {
    fs.mkdirSync(target, { recursive: true });
  }

  // Read source directory
  const entries = fs.readdirSync(source, { withFileTypes: true });

  // Copy each entry
  for (const entry of entries) {
    const sourcePath = path.join(source, entry.name);
    const targetPath = path.join(target, entry.name);

    if (entry.isDirectory()) {
      // Recursively copy subdirectories
      copyDirRecursive(sourcePath, targetPath);
    } else {
      // Copy files
      fs.copyFileSync(sourcePath, targetPath);
      console.log(`Copied: ${sourcePath} -> ${targetPath}`);
    }
  }
}

// Copy plugin directory
copyDirRecursive(sourcePluginDir, targetPluginDir);

console.log('\nPlugin files copied successfully!');
console.log('\nNext steps:');
console.log('1. Install required dependencies:');
console.log('   npm install flatpickr@^4.6.13');
console.log('\n2. Register the plugin in your main.ts:');
console.log('   import MedicationAdminPlugin from \'./plugins/medication-admin\'');
console.log('   app.use(MedicationAdminPlugin)');
console.log('\n3. Use the components in your Vue files:');
console.log('   import { MedicationAdministration } from \'./plugins/medication-admin\'');