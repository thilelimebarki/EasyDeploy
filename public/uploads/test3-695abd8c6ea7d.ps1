# ============================================
# Script PowerShell de test
# ============================================

Write-Host "====================================="
Write-Host " SCRIPT DE TEST - EASYDEPLOY "
Write-Host "====================================="
Write-Host ""

# Affiche le nom du PC
$pcName = $env:COMPUTERNAME
Write-Host "Nom du poste : $pcName"

# Affiche l'utilisateur courant
$user = $env:USERNAME
Write-Host "Utilisateur : $user"

# Date et heure
$date = Get-Date -Format "dd/MM/yyyy HH:mm:ss"
Write-Host "Date d'execution : $date"

Write-Host ""
Write-Host "✅ Le script PowerShell a ete execute avec succes !"
Write-Host ""

# Pause pour voir le resultat
Read-Host "Appuyez sur ENTREE pour fermer"
