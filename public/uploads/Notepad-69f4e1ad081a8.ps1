# ============================================
# Installation de Notepad++
# ============================================

Write-Host "====================================="
Write-Host " Installation de Notepad++ en cours..."
Write-Host "====================================="
Write-Host ""

# URL officielle Notepad++
$downloadUrl = "https://github.com/notepad-plus-plus/notepad-plus-plus/releases/latest/download/npp.8.6.6.Installer.x64.exe"

# Chemin temporaire
$tempPath = "$env:TEMP\npp_installer.exe"

try {
    Write-Host "Téléchargement de Notepad++..."
    Invoke-WebRequest -Uri $downloadUrl -OutFile $tempPath -UseBasicParsing

    Write-Host "Téléchargement terminé."
    Write-Host ""

    Write-Host "Lancement de l'installation silencieuse..."
    Start-Process -FilePath $tempPath -ArgumentList "/S" -Wait

    Write-Host ""
    Write-Host " Notepad++ a été installé avec succès."

} catch {
    Write-Host ""
    Write-Host " Erreur pendant l'installation de Notepad++"
    Write-Host $_
}

# Nettoyage
if (Test-Path $tempPath) {
    Remove-Item $tempPath -Force
}

Write-Host ""
Read-Host "Appuyez sur ENTREE pour fermer"