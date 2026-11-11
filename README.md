## Your Team Forked Your Repo on GitHub
to check theay forg your Repo or not.

```
git remote -v
```
 If they forked it, origin points to their fork, not yours. They need to add yours as a second remote called upstream.

```
cd path/to/project

# Add YOUR repo as upstream
git remote add upstream https://github.com/YOUR_USERNAME/YOUR_REPO.git

# Verify
git remote -v
```

Once upstream is added, they only need these two commands forever: here main is branch name

```
git fetch upstream
git merge upstream/main
 
 or single command 
git pull upstream main

```
