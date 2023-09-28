#!/localdisk/anaconda3/bin/python
from sqlalchemy import create_engine
import pymysql
import numpy as np
import sys

if(len(sys.argv) != 3) :  
    print ("Usage: ./stats.py attribute suppliers_id",sys.argv)
    sys.exit(-1)
col1 = sys.argv[1]  # accept arguments from php
col2 = sys.argv[2]
sql = "SELECT %s FROM Compounds where %s" % (col1,col2)

engine = create_engine('mysql+pymysql://s2321661:asdfzxcv@localhost/s2321661')  # connect to database
r = engine.execute(sql).fetchall()  # run SQL Query and get result
r = np.array(r).flatten()  # change data in to 1-dimension array
tiles = np.percentile(r,[0,25,50,75,100])  # calculate percentile
print('<b>Count:</b>',len(r),'<br>')
print('<b>Min:</b>',round(tiles[0],6),'<br>')
print('<b>25%:</b>',round(tiles[1],6),'<br>')
print('<b>50%:</b>',round(tiles[2],6),'<br>')
print('<b>75%:</b>',round(tiles[3],6),'<br>')
print('<b>Max:</b>',round(tiles[4],6),'<br>')