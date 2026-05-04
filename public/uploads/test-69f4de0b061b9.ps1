# ============================================
# Script de test - Vérification fonctionnement
# ============================================

Write-Host "====================================="
Write-Host "   TEST DU SCRIPT POWERSHELL"
Write-Host "====================================="
Write-Host ""

# Infos machine
Write-Host "Nom du PC : $env:COMPUTERNAME"
Write-Host "Utilisateur : $env:USERNAME"
Write-Host "Date : $(Get-Date)"
Write-Host ""

# Création d'un fichier test sur le bureau
$desktopPath = [Environment]::GetFolderPath("Desktop")
$filePath = "$desktopPath\test_installation.txt"

Write-Host "Création d'un fichier sur le bureau..."
"Script exécuté avec succès le $(Get-Date)" | Out-File $filePath

# Vérification
if (Test-Path $filePath) {
    Write-Host ""
    Write-Host " Fichier créé avec succès : $filePath"
} else {
    Write-Host ""
    Write-Host " Erreur lors de la création du fichier"
}

Write-Host ""
Write-Host "Test terminé !"
Read-Host "Appuyez sur ENTREE pour fermer"