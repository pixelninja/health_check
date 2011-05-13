# Health check

- Version: 1.0
- Author: Phill Gray
- Build Date: 2011-05-13
- Requirements: Symphony 2.2.1

## Installation

- Upload the 'health_check' folder to your Symphony 'extensions' folder.
- Enable it by selecting "Health Check", choose Enable from the with-selected menu, then click Apply.

## Usage

This allows for easy checking of directories that require read/write permissions.

It lists your manifest/cache and manifest/tmp folders as well as any directories specified in any section using the inbuilt Upload Field or the Unique Upload Field.

- **0600** Read and write for owner, nothing for everybody else
- **0644** Read and write for owner, read for everybody else
- **0750** Everything for owner, read and execute for owner's group
- **0755** Everything for owner, read and execute for others
- **0777** Everything for everyone
