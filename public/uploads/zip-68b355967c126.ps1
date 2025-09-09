# Nom du fichier d'installation
$installer = "AcroRdrDCx642400820080_fr_FR.exe"
$downloadUrl = "https://ardownload2.adobe.com/pub/adobe/reader/win/AcrobatDC/2400820080/$installer"
$installerPath = "$env:TEMP\$installer"

Write-Host "Téléchargement d'Adobe Acrobat Reader DC..."
Invoke-WebRequest -Uri $downloadUrl -OutFile $installerPath

Write-Host "Installation d'Adobe Acrobat Reader DC en cours..."
Start-Process -FilePath $installerPath -ArgumentList "/sAll /rs /rps /msi EULA_ACCEPT=YES" -Wait -NoNewWindow

# Nettoyage
Remove-Item $installerPath -Force

Write-Host "Installation terminée."