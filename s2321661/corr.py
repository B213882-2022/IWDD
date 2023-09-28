#!/localdisk/anaconda3/bin/python
from sqlalchemy import create_engine
import pymysql
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
import sys
import io

if(len(sys.argv) != 4) :  
    print ("Usage: ./corr.py properties conditions colnames",sys.argv)
    sys.exit(-1)
prop = sys.argv[1]  # accept arguments from php
sele = sys.argv[2]
colnames = sys.argv[3].split(',')
# print(colnames)
sql = "SELECT %s FROM Compounds where %s" % (prop,sele)

# sql = "SELECT natm,ncar,nnit FROM Compounds where ((natm >=20 and natm <=40)) and ((ManuID = 5)) LIMIT 10"
# colnames = "n atoms,n carbons,n nitrogens".split(',')

engine = create_engine('mysql+pymysql://s2321661:asdfzxcv@localhost/s2321661')  # connect to database
df = pd.read_sql(sql=sql, con=engine)  # load data into DataFrame format
df.columns = colnames
df_corr = df.corr()  # calculate correlations
df_corr = df_corr.reindex(columns = df_corr.columns[::-1])  # reverse the columns order
rownum = df_corr.shape[0]
if(rownum <= 4):
    n = 4
elif(rownum > 4 and rownum <= 6):
    n = rownum
else:
    n = rownum * 0.8 + 0.5
# print(df_corr)
plt.figure(figsize=(n, n))
ax = sns.heatmap(df_corr, vmax=1, vmin=-1, annot=True, linewidths=0.5, fmt='.3f', cmap="coolwarm",annot_kws={'fontstyle': 'italic', 'color':'black','alpha': 0.9})
ax.xaxis.tick_top()  # set x axis to top
ax.tick_params(left=False, top=False)  # disappear the ticks at axes
plt.yticks(rotation=0)  # rotate the y axis text to horizontal
plt.xticks(rotation=45)  
ax.set_ylim([0, rownum])  # in case the bug in matpoltlib 3.1.1 (which shows only half of top and bottom rows)
plt.tight_layout()  # in case any text is out of boundaries
image = io.BytesIO()
plt.savefig(image,format='png')
sys.stdout.buffer.write(image.getvalue())
# plt.show()
con.close()